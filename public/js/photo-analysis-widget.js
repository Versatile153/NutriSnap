class PhotoAnalysisWidget {
    constructor(apiUrl, token) {
        this.apiUrl = apiUrl;
        this.token = token;
    }

    init(containerId) {
        const container = document.getElementById(containerId);
        if (!container) {
            console.error('Container not found: #' + containerId);
            return;
        }

        // Create widget UI with Tailwind CSS for consistency with NutriSnap
        container.innerHTML = `
            <div class="bg-gray-800/80 p-6 rounded-2xl shadow-lg animate-fadeIn">
                <h3 class="text-xl font-semibold text-white mb-4">Analyze Food Photo</h3>
                <form id="photoAnalysisForm" class="space-y-4">
                    <input type="file" id="photoInput" accept="image/*" class="block w-full text-gray-300 bg-gray-700 rounded-lg p-2">
                    <button type="submit" class="bg-gradient-to-r from-pink-600 to-purple-600 text-white px-6 py-2 rounded-lg hover:from-pink-700 hover:to-purple-700 transition-all duration-300">Analyze</button>
                </form>
                <div id="analysisResults" class="mt-4 text-gray-300"></div>
            </div>
        `;

        const form = container.querySelector('#photoAnalysisForm');
        form.addEventListener('submit', (e) => this.handleSubmit(e));
    }

    async handleSubmit(e) {
        e.preventDefault();
        const photoInput = document.getElementById('photoInput');
        const resultsDiv = document.getElementById('analysisResults');

        if (!photoInput.files[0]) {
            resultsDiv.innerHTML = '<p class="text-red-400">Please select an image.</p>';
            return;
        }

        const formData = new FormData();
        formData.append('photo', photoInput.files[0]);

        resultsDiv.innerHTML = '<p>Loading...</p>';

        try {
            const response = await fetch(`${this.apiUrl}/api/seller/analyze-photo`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${this.token}`,
                },
                body: formData,
            });

            const data = await response.json();
            if (response.ok) {
                resultsDiv.innerHTML = `
                    <p><strong>Calories:</strong> ${data.calories} kcal</p>
                    <p><strong>Ingredients:</strong> ${data.ingredients.join(', ')}</p>
                    <img src="${data.image_url}" alt="Analyzed Food" class="mt-2 w-full max-w-xs rounded-lg">
                `;
            } else {
                resultsDiv.innerHTML = `<p class="text-red-400">Error: ${data.errors ? Object.values(data.errors).join(', ') : 'Analysis failed'}</p>`;
            }
        } catch (error) {
            resultsDiv.innerHTML = `<p class="text-red-400">Error: ${error.message}</p>`;
        }
    }
}

// Usage example
// const widget = new PhotoAnalysisWidget('http://127.0.0.1:8000', 'your-api-token');
// widget.init('photo-analysis-container');