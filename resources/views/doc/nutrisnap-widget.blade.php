
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriSnap Widget Embedding Guide</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'Noto Serif KR', 'ui-sans-serif', 'system-ui'] },
                    colors: { border: '#4B5563' }
                }
            }
        }
    </script>
    <style>
        body { background: #111827; color: #F3F4F6; }
        h1, h2, h3 { color: #F3F4F6; }
        pre { background: #1F2937; padding: 15px; border-radius: 5px; overflow-x: auto; }
        code { font-family: monospace; color: #D1D5DB; }
        a { color: #F472B6; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body class="font-sans min-h-screen">
    <div class="container mx-auto px-4 sm:px-6 py-10">
        <h1 class="text-3xl font-bold mb-4">NutriSnap Widget Embedding Guide</h1>
        <p class="mb-6">The NutriSnap Widget allows users to analyze food photos directly on your website. Follow these steps to embed the widget.</p>

        <h2 class="text-2xl font-semibold mb-3">Step 1: Include the Widget Script</h2>
        <p class="mb-2">Add the following script tag to your HTML <code>&lt;head&gt;</code> or <code>&lt;body&gt;</code>:</p>
        <pre><code>&lt;script src="https://bincone.apexjets.org/js/nutrisnap-widget.min.js" async&gt;&lt;/script&gt;</code></pre>

        <h2 class="text-2xl font-semibold mb-3">Step 2: Add the Widget Container</h2>
        <p class="mb-2">Place a <code>&lt;div&gt;</code> element in your HTML where you want the widget to appear. Include the required data attributes to configure the widget:</p>
        <pre><code>&lt;div id="nutrisnap-widget" 
     data-nutrisnap-widget
     data-api-url="https://bincone.apexjets.org/api"
     data-token="your-api-token"
     data-result-container=".product-page-results"
     data-language="en"&gt;&lt;/div&gt;</code></pre>

        <h3 class="text-xl font-semibold mb-2">Data Attributes</h3>
        <ul class="list-disc pl-5 mb-6">
            <li><code>data-nutrisnap-widget</code>: Marks the container for widget initialization.</li>
            <li><code>data-api-url</code>: The base URL of the NutriSnap API (default: <code>https://bincone.apexjets.org/api</code>).</li>
            <li><code>data-token</code>: Your API token (required for authenticated requests).</li>
            <li><code>data-result-container</code>: CSS selector for the container where results will be appended (default: <code>.product-page-results</code>).</li>
            <li><code>data-language</code>: Language for the widget UI (<code>en</code> for English, <code>ko</code> for Korean, default: <code>en</code>).</li>
        </ul>

        <h2 class="text-2xl font-semibold mb-3">Step 3: Add a Results Container (Optional)</h2>
        <p class="mb-2">If you want analysis results to appear in a specific section of your page, add a container with the specified selector:</p>
        <pre><code>&lt;div class="product-page-results"&gt;&lt;/div&gt;</code></pre>

        <h2 class="text-2xl font-semibold mb-3">Step 4: Obtain an API Token</h2>
        <p class="mb-2">To use the widget, you need an API token. Register or log in to obtain a token:</p>
        <ul class="list-disc pl-5 mb-6">
            <li><strong>Register</strong>: Send a POST request to <code>https://bincone.apexjets.org/api/seller/register</code>.</li>
            <li><strong>Login</strong>: Send a POST request to <code>https://bincone.apexjets.org/api/seller/login</code>.</li>
        </ul>
        <p class="mb-6">For API details, visit <a href="/api-docs">API Documentation</a>.</p>

        <h2 class="text-2xl font-semibold mb-3">Example HTML</h2>
        <pre><code>&lt;!DOCTYPE html&gt;
&lt;html lang="en"&gt;
&lt;head&gt;
    &lt;meta charset="UTF-8"&gt;
    &lt;meta name="viewport" content="width=device-width, initial-scale=1.0"&gt;
    &lt;title&gt;NutriSnap Widget Demo&lt;/title&gt;
    &lt;script src="https://bincone.apexjets.org/js/nutrisnap-widget.min.js" async&gt;&lt;/script&gt;
&lt;/head&gt;
&lt;body&gt;
    &lt;div id="nutrisnap-widget" 
         data-nutrisnap-widget
         data-api-url="https://bincone.apexjets.org/api"
         data-token="your-api-token"
         data-result-container=".product-page-results"
         data-language="en"&gt;&lt;/div&gt;
    &lt;div class="product-page-results"&gt;&lt;/div&gt;
&lt;/body&gt;
&lt;/html&gt;</code></pre>

        <h2 class="text-2xl font-semibold mb-3">Notes</h2>
        <ul class="list-disc pl-5 mb-6">
            <li>Ensure the <code>data-token</code> is valid to access the API.</li>
            <li>The widget supports English (<code>en</code>) and Korean (<code>ko</code>) languages.</li>
            <li>The widget loads dependencies (Tailwind CSS, Alpine.js, Feather Icons, SweetAlert2) automatically.</li>
            <li>For support, contact <a href="mailto:support@bincone.apexjets.org">support@bincone.apexjets.org</a>.</li>
        </ul>
    </div>
</body>
</html>
