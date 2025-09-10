// NutriSnapWidget class for photo analysis
class NutriSnapWidget {
    constructor(containerId, config = {}) {
        this.containerId = containerId;
        this.apiUrl = config.apiUrl || 'https://bincone.apexjets.org/api';
        this.token = config.token || '';
        this.productContainerSelector = config.productContainerSelector || '.product-page-results';
        this.language = config.language || 'en';
        this.isFormVisible = false;
    }

    async init() {
        const container = document.getElementById(this.containerId);
        if (!container) {
            console.error('NutriSnapWidget: Container not found:', this.containerId);
            return;
        }

        // Load dependencies dynamically
        await this.loadDependencies();

        // Inject widget HTML with Alpine.js for reactivity
        container.classList.add('nutrisnap-widget-container');
        container.innerHTML = `
            <div x-data="{ isFormVisible: false, analysisType: 'meal', selectedLanguage: '${this.language}' }">
                <button @click="isFormVisible = !isFormVisible" id="toggleWidget" 
                        class="fixed bottom-4 left-4 bg-pink-400 text-white p-3 rounded-full shadow-lg hover:bg-pink-500 transition-all z-50" 
                        aria-label="Open photo analysis">
                    <i data-feather="camera" class="w-6 h-6"></i>
                </button>
                <div x-show="isFormVisible" id="widgetFormContainer" 
                     class="fixed bottom-16 left-4 w-80 bg-gray-800 p-4 rounded-lg shadow-xl z-40 animate-slideUp">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-lg font-semibold text-white" x-text="selectedLanguage === 'en' ? 'Photo Analysis' : '사진 분석'"></h3>
                        <button @click="isFormVisible = false" id="closeWidget" 
                                class="text-gray-300 hover:text-white text-xl" aria-label="Close">&times;</button>
                    </div>
                    <form id="photoAnalysisForm" class="space-y-3">
                        <input type="file" id="photoInput" name="photo" accept="image/jpeg,image/png,image/jpg" 
                               class="block w-full text-sm text-gray-300 bg-gray-700 rounded-md p-2">
                        <input type="text" id="productId" name="product_id" 
                               :placeholder="selectedLanguage === 'en' ? 'Product ID' : '제품 ID'" 
                               class="block w-full text-sm text-gray-300 bg-gray-700 rounded-md p-2">
                        <input type="text" id="mealId" name="meal_id" 
                               x-show="analysisType === 'leftover'" 
                               :placeholder="selectedLanguage === 'en' ? 'Meal ID (for leftovers)' : '식사 ID (남은 음식용)'" 
                               class="block w-full text-sm text-gray-300 bg-gray-700 rounded-md p-2">
                        <select @change="analysisType = $event.target.value" 
                                class="w-full text-sm bg-gray-700 text-gray-300 rounded-md p-2">
                            <option value="meal" x-text="selectedLanguage === 'en' ? 'Meal Analysis' : '식사 분석'"></option>
                            <option value="leftover" x-text="selectedLanguage === 'en' ? 'Leftover Analysis' : '남은 음식 분석'"></option>
                        </select>
                        <select x-model="selectedLanguage" class="w-full text-sm bg-gray-700 text-gray-300 rounded-md p-2">
                            <option value="en">English</option>
                            <option value="ko">한국어</option>
                        </select>
                        <button type="submit" 
                                class="bg-pink-400 text-white px-4 py-2 rounded-md hover:bg-pink-500 transition-all w-full text-sm"
                                x-text="selectedLanguage === 'en' ? 'Analyze' : '분석하기'"></button>
                    </form>
                    <div id="analysisResults" class="mt-3 text-sm text-gray-300"></div>
                </div>
            </div>
            <style>
                @keyframes slideUp { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
                @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
                .nutrisnap-widget-container {
                    font-family: 'Inter', 'Noto Serif KR', sans-serif;
                }
                .nutrisnap-widget-container input, .nutrisnap-widget-container button, .nutrisnap-widget-container select {
                    transition: all 0.3s ease;
                }
                .nutrisnap-widget-container input:focus, .nutrisnap-widget-container button:focus, .nutrisnap-widget-container select:focus {
                    outline: none;
                    border-color: #f472b6;
                    box-shadow: 0 0 0 2px rgba(244, 114, 182, 0.2);
                }
                .nutrisnap-widget-container .results-card {
                    animation: fadeIn 0.3s ease-in-out;
                }
            </style>
        `;

        // Initialize Feather icons
        if (window.feather) feather.replace();

        // Bind event listeners
        const form = container.querySelector('#photoAnalysisForm');
        form.addEventListener('submit', (e) => this.handleSubmit(e));
    }

    async loadDependencies() {
        const loadScript = (src) => {
            return new Promise((resolve, reject) => {
                const script = document.createElement('script');
                script.src = src;
                script.async = true;
                script.onload = resolve;
                script.onerror = () => reject(new Error(`Failed to load script: ${src}`));
                document.head.appendChild(script);
            });
        };

        const loadStyle = (href) => {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = href;
            document.head.appendChild(link);
        };

        try {
            // Load Tailwind CSS
            await loadScript('https://cdn.tailwindcss.com');
            if (window.tailwind) {
                window.tailwind.config = {
                    theme: {
                        extend: {
                            fontFamily: {
                                sans: ['Inter', 'Noto Serif KR', 'ui-sans-serif', 'system-ui']
                            },
                            colors: {
                                border: '#4B5563'
                            }
                        }
                    }
                };
            }

            // Load fonts
            loadStyle('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Noto+Serif+KR:wght@400;700&display=swap');

            // Load Feather icons
            await loadScript('https://unpkg.com/feather-icons');

            // Load Alpine.js
            await loadScript('https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js');

            // Load SweetAlert2
            await loadScript('https://cdn.jsdelivr.net/npm/sweetalert2@11');
        } catch (error) {
            console.error('NutriSnapWidget: Failed to load dependencies:', error);
            if (window.Swal) {
                Swal.fire({
                    title: this.language === 'en' ? 'Error' : '오류',
                    text: this.language === 'en' ? 'Failed to load widget dependencies. Please try again later.' : '위젯 의존성을 로드하지 못했습니다. 나중에 다시 시도해주세요.',
                    icon: 'error'
                });
            }
        }
    }

    async handleSubmit(e) {
        e.preventDefault();
        const photoInput = document.getElementById('photoInput');
        const productIdInput = document.getElementById('productId');
        const mealIdInput = document.getElementById('mealId');
        const analysisType = document.querySelector('select').value;
        const resultsDiv = document.getElementById('analysisResults');
        const productContainer = document.querySelector(this.productContainerSelector);
        const lang = document.querySelector('select[x-model="selectedLanguage"]').value;

        if (!this.token) {
            Swal.fire({
                title: lang === 'en' ? 'Configuration Error' : '설정 오류',
                text: lang === 'en' ? 'API token is missing. Please provide a valid token.' : 'API 토큰이 없습니다. 유효한 토큰을 제공해주세요.',
                icon: 'error'
            });
            return;
        }

        if (!photoInput.files[0]) {
            Swal.fire({
                title: lang === 'en' ? 'Missing File' : '파일 누락',
                text: lang === 'en' ? 'Please select an image to upload.' : '업로드할 이미지를 선택해주세요.',
                icon: 'warning'
            });
            return;
        }

        const file = photoInput.files[0];
        if (!['image/jpeg', 'image/png', 'image/jpg'].includes(file.type)) {
            Swal.fire({
                title: lang === 'en' ? 'Invalid File' : '잘못된 파일',
                text: lang === 'en' ? 'Please select a valid image (JPEG/PNG).' : '유효한 이미지(JPEG/PNG)를 선택해주세요.',
                icon: 'error'
            });
            return;
        }
        if (file.size > 2 * 1024 * 1024) {
            Swal.fire({
                title: lang === 'en' ? 'File Too Large' : '파일 크기 초과',
                text: lang === 'en' ? 'Image must be under 2MB.' : '이미지는 2MB 이하여야 합니다.',
                icon: 'error'
            });
            return;
        }

        const productId = productIdInput.value;
        if (!productId) {
            Swal.fire({
                title: lang === 'en' ? 'Missing Product ID' : '제품 ID 누락',
                text: lang === 'en' ? 'Please enter a product ID.' : '제품 ID를 입력해주세요.',
                icon: 'warning'
            });
            return;
        }

        const mealId = mealIdInput.value;
        if (analysisType === 'leftover' && !mealId) {
            Swal.fire({
                title: lang === 'en' ? 'Missing Meal ID' : '식사 ID 누락',
                text: lang === 'en' ? 'Please enter a meal ID for leftover analysis.' : '남은 음식 분석을 위해 식사 ID를 입력해주세요.',
                icon: 'warning'
            });
            return;
        }

        const formData = new FormData();
        formData.append('photo', file);
        formData.append('product_id', productId);
        if (analysisType === 'leftover') {
            formData.append('meal_id', mealId);
        }

        resultsDiv.innerHTML = `<p class="text-sm">${lang === 'en' ? 'Loading...' : '로딩 중...'}</p>`;

        try {
            const endpoint = analysisType === 'leftover' ? '/seller/analyze-leftover' : '/seller/analyze-photo';
            const response = await fetch(`${this.apiUrl}${endpoint}`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${this.token}`,
                    'Accept': 'application/json',
                },
                body: formData,
            });

            const data = await response.json();

            if (!response.ok) {
                if (data.errors && data.errors.photo && data.errors.photo.includes('No food items detected')) {
                    Swal.fire({
                        title: lang === 'en' ? 'No Food Detected' : '음식 미감지',
                        text: lang === 'en' ? 'No food photo detected, please upload a food item.' : '음식 사진이 감지되지 않았습니다. 음식 사진을 업로드해주세요.',
                        icon: 'warning'
                    });
                } else {
                    Swal.fire({
                        title: lang === 'en' ? 'Analysis Failed' : '분석 실패',
                        text: data.message || (lang === 'en' ? 'Unable to process the image.' : '이미지를 처리할 수 없습니다.'),
                        icon: 'error'
                    });
                }
                resultsDiv.innerHTML = '';
                return;
            }

            if (data.analysis_id) {
                Swal.fire({
                    title: lang === 'en' ? 'Success!' : '성공!',
                    text: lang === 'en' ? 'Image analysis completed successfully.' : '이미지 분석이 성공적으로 완료되었습니다.',
                    icon: 'success'
                });

                const resultHtml = `
                    <div class="results-card bg-gray-700 p-4 rounded-md shadow-md space-y-3">
                        <p class="text-sm text-white"><strong>${lang === 'en' ? 'Analysis ID' : '분석 ID'}:</strong> ${data.analysis_id}</p>
                        <p class="text-sm text-white"><strong>${lang === 'en' ? 'Calories' : '칼로리'}:</strong> ${data.calories || 'N/A'} kcal</p>
                        <p class="text-sm text-white"><strong>${lang === 'en' ? 'Ingredients' : '성분'}:</strong> ${data.ingredients ? data.ingredients.join(', ') : 'N/A'}</p>
                    </div>
                `;
                resultsDiv.innerHTML = resultHtml;
                if (productContainer) {
                    const productResultDiv = document.createElement('div');
                    productResultDiv.className = 'product-analysis-results mt-4';
                    productResultDiv.innerHTML = resultHtml;
                    productContainer.appendChild(productResultDiv);
                }
            } else {
                Swal.fire({
                    title: lang === 'en' ? 'Error' : '오류',
                    text: lang === 'en' ? 'Analysis failed. Please try again.' : '분석에 실패했습니다. 다시 시도해주세요.',
                    icon: 'error'
                });
                resultsDiv.innerHTML = '';
            }
        } catch (error) {
            Swal.fire({
                title: lang === 'en' ? 'Network Error' : '네트워크 오류',
                text: error.message,
                icon: 'error'
            });
            resultsDiv.innerHTML = '';
        }
    }

    // Expose method to toggle widget visibility
    toggleWidget() {
        const formContainer = document.getElementById('widgetFormContainer');
        if (formContainer) {
            this.isFormVisible = !this.isFormVisible;
            formContainer.setAttribute('x-show', this.isFormVisible);
        }
    }
}

// Auto-initialize widget based on data attributes
document.addEventListener('DOMContentLoaded', () => {
    const containers = document.querySelectorAll('[data-nutrisnap-widget]');
    containers.forEach(container => {
        const config = {
            apiUrl: container.dataset.apiUrl || 'https://bincone.apexjets.org/api',
            token: container.dataset.token || '',
            productContainerSelector: container.dataset.resultContainer || '.product-page-results',
            language: container.dataset.language || 'en'
        };
        const widget = new NutriSnapWidget(container.id, config);
        widget.init();
    });
});