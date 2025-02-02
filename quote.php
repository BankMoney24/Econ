<?php
// config.php configuration remains same as previous

// Function to fetch exchange rates using ExchangeRate-API (free tier)
function getExchangeRates() {
    $apiKey = 'YOUR_API_KEY'; // Sign up at https://www.exchangerate-api.com/
    $url = "https://v6.exchangerate-api.com/v6/{$apiKey}/latest/USD";
    
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);
    
    return json_decode($response, true);
}

$rates = getExchangeRates();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rates & Quotes - EconestModals</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Three.js for 3D animations -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    
    <!-- Custom CSS -->
    <style>
        .container-3d {
            height: 300px;
            position: relative;
            overflow: hidden;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
        .rate-card {
            transition: transform 0.3s ease;
            cursor: pointer;
        }
        
        .rate-card:hover {
            transform: translateY(-5px);
        }
        
        .currency-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px 10px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.9);
        }
        
        .quote-form {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .animated-bg {
            background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }
        
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
    </style>
</head>
<body>

<!-- Header -->
<header class="header-area header-sticky">
    <!-- Your existing header code -->
</header>

<!-- Main Content -->
<div class="animated-bg py-5">
    <div class="container">
        <!-- 3D Container Animation -->
        <div class="container-3d" id="container3D"></div>
        
        <!-- Quote Calculator -->
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="quote-form">
                    <h2 class="text-center mb-4">Calculate Shipping Quote</h2>
                    <form id="quoteForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Container Type</label>
                                <select class="form-select" id="containerType" required>
                                    <option value="">Select Container</option>
                                    <option value="20ft">20ft Standard</option>
                                    <option value="40ft">40ft Standard</option>
                                    <option value="40ft-hc">40ft High Cube</option>
                                    <option value="reefer">Refrigerated</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Currency</label>
                                <select class="form-select" id="currency">
                                    <option value="USD">USD - US Dollar</option>
                                    <option value="EUR">EUR - Euro</option>
                                    <option value="GBP">GBP - British Pound</option>
                                    <option value="JPY">JPY - Japanese Yen</option>
                                    <option value="AUD">AUD - Australian Dollar</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Origin</label>
                                <input type="text" class="form-control" id="origin" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Destination</label>
                                <input type="text" class="form-control" id="destination" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Cargo Type</label>
                            <input type="text" class="form-control" id="cargoType" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">Calculate Quote</button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Live Exchange Rates -->
        <div class="row mt-5">
            <h3 class="text-center text-white mb-4">Live Exchange Rates</h3>
            <?php
            $mainCurrencies = ['EUR', 'GBP', 'JPY', 'AUD', 'CAD'];
            foreach ($mainCurrencies as $currency) {
                if (isset($rates['conversion_rates'][$currency])) {
                    $rate = $rates['conversion_rates'][$currency];
                    echo <<<HTML
                    <div class="col-md-4 col-lg-2 mb-3">
                        <div class="card rate-card h-100">
                            <div class="card-body text-center">
                                <h5 class="card-title">$currency</h5>
                                <p class="card-text display-6">$rate</p>
                                <small class="text-muted">1 USD = $rate $currency</small>
                            </div>
                        </div>
                    </div>
                    HTML;
                }
            }
            ?>
        </div>
    </div>
</div>

<!-- JavaScript for 3D Animation and Calculations -->
<script>
// Three.js Container Animation
const scene = new THREE.Scene();
const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
const renderer = new THREE.WebGLRenderer();
const container = document.getElementById('container3D');

renderer.setSize(container.offsetWidth, container.offsetHeight);
container.appendChild(renderer.domElement);

// Create container geometry
const geometry = new THREE.BoxGeometry(2, 2, 5);
const material = new THREE.MeshPhongMaterial({
    color: 0x2194ce,
    specular: 0x555555,
    shininess: 30
});
const container3D = new THREE.Mesh(geometry, material);
scene.add(container3D);

// Add lights
const light = new THREE.DirectionalLight(0xffffff, 1);
light.position.set(0, 1, 1).normalize();
scene.add(light);
scene.add(new THREE.AmbientLight(0x404040));

camera.position.z = 7;

// Animation loop
function animate() {
    requestAnimationFrame(animate);
    container3D.rotation.x += 0.01;
    container3D.rotation.y += 0.01;
    renderer.render(scene, camera);
}
animate();

// Quote Calculator
document.getElementById('quoteForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const containerType = document.getElementById('containerType').value;
    const currency = document.getElementById('currency').value;
    const origin = document.getElementById('origin').value;
    const destination = document.getElementById('destination').value;
    
    // Basic rate calculation (you should replace with your actual pricing logic)
    let baseRate = {
        '20ft': 2000,
        '40ft': 3500,
        '40ft-hc': 4000,
        'reefer': 5500
    }[containerType] || 0;
    
    // Convert to selected currency using rates from PHP
    const rates = <?php echo json_encode($rates['conversion_rates'] ?? []); ?>;
    const convertedRate = baseRate * (rates[currency] || 1);
    
    // Show result in modal
    const modalHtml = `
        
        <div class="modal fade" id="quoteModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Shipping Quote</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Route:</strong> ${origin} to ${destination}</p>
                        <p><strong>Container:</strong> ${containerType}</p>
                        <p><strong>Estimated Rate:</strong> ${currency} ${convertedRate.toFixed(2)}</p>
                        <small class="text-muted">*Rates are approximate and subject to change</small>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modal = new bootstrap.Modal(document.getElementById('quoteModal'));
    modal.show();
});
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>