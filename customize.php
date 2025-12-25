<?php
$prodName = isset($_GET['prod']) ? htmlspecialchars(urldecode($_GET['prod'])) : 'Açaí 500ml';
$basePrice = isset($_GET['price']) ? floatval($_GET['price']) : 20.00;
$prodImg = isset($_GET['img']) ? htmlspecialchars(urldecode($_GET['img'])) : 'assets/logo.png'; // Fallback
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Montar seu Açaí - Zé do Açaí</title>
    <style>
        :root {
            --primary-purple: #6f2c91;
            --primary-yellow: #ffcc01;
            --text-dark: #333;
            --bg-gray: #f5f5f5;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--bg-gray);
            color: var(--text-dark);
            padding-bottom: 100px; /* Space for fixed footer */
        }

        .header {
            background-color: var(--primary-purple);
            color: white;
            padding: 15px;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .header h1 {
            margin: 0;
            font-size: 1.2rem;
        }
        
        .header .back-btn {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        .product-summary {
            background: white;
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        .product-summary img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid var(--primary-purple);
            margin-bottom: 10px;
        }

        .section {
            background: white;
            margin-top: 15px;
            padding: 20px;
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: bold;
            color: var(--primary-purple);
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .section-subtitle {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 15px;
        }

        .option-group {
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .option-item {
            display: flex;
            align-items: center;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .option-item:hover {
            border-color: var(--primary-purple);
            background-color: #fcf5ff;
        }
        
        .option-item input {
            margin-right: 15px;
            transform: scale(1.3);
            accent-color: var(--primary-purple);
        }
        
        .option-details {
            flex-grow: 1;
        }
        
        .option-name {
            font-weight: 500;
        }
        
        .option-price {
            color: var(--primary-purple);
            font-weight: bold;
            font-size: 0.9rem;
        }

        .btn-plus-minus {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: 1px solid #ddd;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            cursor: pointer;
        }

        .footer-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: white;
            padding: 15px 20px;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-sizing: border-box;
        }

        .total-price {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--text-dark);
        }
        
        .total-price small {
            display: block;
            font-size: 0.8rem;
            color: #666;
            font-weight: normal;
        }

        .btn-finish {
            background-color: var(--primary-purple);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: bold;
            font-size: 1rem;
            cursor: pointer;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <div class="header">
        <a href="index.html" class="back-btn"><i class="fa-solid fa-arrow-left"></i></a>
        <h1>Monte seu Açaí</h1>
    </div>

    <div class="product-summary">
        <img src="<?php echo $prodImg; ?>" alt="Açaí">
        <h2><?php echo $prodName; ?></h2>
        <p>Base: R$ <?php echo number_format($basePrice, 2, ',', '.'); ?></p>
    </div>

    <form id="customize-form" action="checkout.php" method="GET">
        <input type="hidden" name="prod" id="final-prod-name" value="<?php echo $prodName; ?>">
        <input type="hidden" name="price" id="final-price-input" value="<?php echo $basePrice; ?>">
        <input type="hidden" name="img" value="<?php echo $prodImg; ?>">
        <input type="hidden" name="desc" id="final-desc" value="">

        <!-- Step 1: Base Flavor -->
        <div class="section">
            <div class="section-title">1. Escolha a Base <i class="fa-solid fa-ice-cream"></i></div>
            <div class="section-subtitle">Selecione 1 opção (Obrigatório)</div>
            <div class="option-group">
                <label class="option-item">
                    <input type="radio" name="base_flavor" value="Tradicional" checked data-price="0">
                    <div class="option-details"><span class="option-name">Açaí Tradicional</span></div>
                </label>
                <label class="option-item">
                    <input type="radio" name="base_flavor" value="Trufado" data-price="2.00">
                    <div class="option-details">
                        <span class="option-name">Açaí Trufado</span>
                        <div class="option-price">+ R$ 2,00</div>
                    </div>
                </label>
                <label class="option-item">
                    <input type="radio" name="base_flavor" value="Morango" data-price="0">
                    <div class="option-details"><span class="option-name">Creme de Morango</span></div>
                </label>
                <label class="option-item">
                    <input type="radio" name="base_flavor" value="Cupuaçu" data-price="0">
                    <div class="option-details"><span class="option-name">Creme de Cupuaçu</span></div>
                </label>
                <label class="option-item">
                    <input type="radio" name="base_flavor" value="Misto" data-price="0">
                    <div class="option-details"><span class="option-name">Misto (Açaí + Cupuaçu)</span></div>
                </label>
                <label class="option-item">
                    <input type="radio" name="base_flavor" value="Zero" data-price="3.00">
                    <div class="option-details">
                        <span class="option-name">Açaí Zero Açúcar</span>
                        <div class="option-price">+ R$ 3,00</div>
                    </div>
                </label>
            </div>
        </div>

        <!-- Step 2: Fruits -->
        <div class="section">
            <div class="section-title">2. Frutas <i class="fa-solid fa-lemon"></i></div>
            <div class="section-subtitle">Escolha até 3 opções</div>
            <div class="option-group">
                <label class="option-item"><input type="checkbox" name="fruits[]" value="Morango" data-price="0"><span class="option-name">Morango</span></label>
                <label class="option-item"><input type="checkbox" name="fruits[]" value="Banana" data-price="0"><span class="option-name">Banana</span></label>
                <label class="option-item"><input type="checkbox" name="fruits[]" value="Kiwi" data-price="0"><span class="option-name">Kiwi</span></label>
                <label class="option-item"><input type="checkbox" name="fruits[]" value="Manga" data-price="0"><span class="option-name">Manga</span></label>
                <label class="option-item"><input type="checkbox" name="fruits[]" value="Abacaxi" data-price="0"><span class="option-name">Abacaxi</span></label>
                <label class="option-item"><input type="checkbox" name="fruits[]" value="Uva" data-price="0"><span class="option-name">Uva Verde</span></label>
            </div>
        </div>

        <!-- Step 3: Complimentary Toppings -->
        <div class="section">
            <div class="section-title">3. Complementos Grátis <i class="fa-solid fa-spoon"></i></div>
            <div class="section-subtitle">Escolha à vontade</div>
            <div class="option-group">
                <label class="option-item"><input type="checkbox" name="free_toppings[]" value="Leite Ninho" data-price="0"><span class="option-name">Leite Ninho</span></label>
                <label class="option-item"><input type="checkbox" name="free_toppings[]" value="Paçoca" data-price="0"><span class="option-name">Paçoca</span></label>
                <label class="option-item"><input type="checkbox" name="free_toppings[]" value="Granola" data-price="0"><span class="option-name">Granola</span></label>
                <label class="option-item"><input type="checkbox" name="free_toppings[]" value="Leite Condensado" data-price="0"><span class="option-name">Leite Condensado</span></label>
                <label class="option-item"><input type="checkbox" name="free_toppings[]" value="Mel" data-price="0"><span class="option-name">Mel</span></label>
                <label class="option-item"><input type="checkbox" name="free_toppings[]" value="Sucrilhos" data-price="0"><span class="option-name">Sucrilhos</span></label>
                <label class="option-item"><input type="checkbox" name="free_toppings[]" value="Coco Ralado" data-price="0"><span class="option-name">Coco Ralado</span></label>
                <label class="option-item"><input type="checkbox" name="free_toppings[]" value="Ovomaltine" data-price="0"><span class="option-name">Ovomaltine</span></label>
                <label class="option-item"><input type="checkbox" name="free_toppings[]" value="Chocoball" data-price="0"><span class="option-name">Chocoball</span></label>
                <label class="option-item"><input type="checkbox" name="free_toppings[]" value="Confete" data-price="0"><span class="option-name">Confete</span></label>
                <label class="option-item"><input type="checkbox" name="free_toppings[]" value="Jujuba" data-price="0"><span class="option-name">Jujuba</span></label>
                <label class="option-item"><input type="checkbox" name="free_toppings[]" value="Amendoim" data-price="0"><span class="option-name">Amendoim</span></label>
            </div>
        </div>

        <!-- Step 4: Premium Extras -->
        <div class="section">
            <div class="section-title">4. Turbinar (Premium) <i class="fa-solid fa-bolt"></i></div>
            <div class="section-subtitle">Adicione sabor extra (Pago)</div>
            <div class="option-group">
                <label class="option-item">
                    <input type="checkbox" name="premium[]" value="Nutella" data-price="5.00">
                    <div class="option-details">
                        <span class="option-name">Nutella Original</span>
                        <div class="option-price">+ R$ 5,00</div>
                    </div>
                </label>
                <label class="option-item">
                    <input type="checkbox" name="premium[]" value="Kinder Bueno" data-price="6.00">
                    <div class="option-details">
                        <span class="option-name">Kinder Bueno (Pedaços)</span>
                        <div class="option-price">+ R$ 6,00</div>
                    </div>
                </label>
                <label class="option-item">
                    <input type="checkbox" name="premium[]" value="KitKat" data-price="4.00">
                    <div class="option-details">
                        <span class="option-name">KitKat</span>
                        <div class="option-price">+ R$ 4,00</div>
                    </div>
                </label>
                <label class="option-item">
                    <input type="checkbox" name="premium[]" value="Bis" data-price="3.00">
                    <div class="option-details">
                        <span class="option-name">Bis (Lacta)</span>
                        <div class="option-price">+ R$ 3,00</div>
                    </div>
                </label>
                <label class="option-item">
                    <input type="checkbox" name="premium[]" value="Gotas de Chocolate" data-price="3.00">
                    <div class="option-details">
                        <span class="option-name">Gotas de Chocolate</span>
                        <div class="option-price">+ R$ 3,00</div>
                    </div>
                </label>
                <label class="option-item">
                    <input type="checkbox" name="premium[]" value="Chantilly" data-price="4.00">
                    <div class="option-details">
                        <span class="option-name">Chantilly</span>
                        <div class="option-price">+ R$ 4,00</div>
                    </div>
                </label>
                <label class="option-item">
                    <input type="checkbox" name="premium[]" value="Marshmallow" data-price="3.00">
                    <div class="option-details">
                        <span class="option-name">Marshmallow</span>
                        <div class="option-price">+ R$ 3,00</div>
                    </div>
                </label>
                <label class="option-item">
                    <input type="checkbox" name="premium[]" value="Mouse de Maracujá" data-price="4.50">
                    <div class="option-details">
                        <span class="option-name">Mouse de Maracujá</span>
                        <div class="option-price">+ R$ 4,50</div>
                    </div>
                </label>
                <label class="option-item">
                    <input type="checkbox" name="premium[]" value="Ferrero Rocher" data-price="7.00">
                    <div class="option-details">
                        <span class="option-name">Ferrero Rocher (Unidade)</span>
                        <div class="option-price">+ R$ 7,00</div>
                    </div>
                </label>
            </div>
        </div>

        <!-- Step 5: Caldas -->
        <div class="section">
            <div class="section-title">5. Caldas <i class="fa-solid fa-fill-drip"></i></div>
            <div class="option-group">
                 <label class="option-item"><input type="checkbox" name="caldas[]" value="Calda de Morango" data-price="0"><span class="option-name">Calda de Morango</span></label>
                 <label class="option-item"><input type="checkbox" name="caldas[]" value="Calda de Chocolate" data-price="0"><span class="option-name">Calda de Chocolate</span></label>
                 <label class="option-item"><input type="checkbox" name="caldas[]" value="Calda de Caramelo" data-price="0"><span class="option-name">Calda de Caramelo</span></label>
                 <label class="option-item"><input type="checkbox" name="caldas[]" value="Calda de Menta" data-price="0"><span class="option-name">Calda de Menta</span></label>
            </div>
        </div>

        <div class="footer-bar">
            <div class="total-price">
                <small>Total do Pedido:</small>
                <span id="display-total">R$ <?php echo number_format($basePrice, 2, ',', '.'); ?></span>
            </div>
            <button type="submit" class="btn-finish" onclick="prepareSubmit(event)">
                Finalizar <i class="fa-solid fa-check"></i>
            </button>
        </div>
    </form>

    <script>
        const initialBasePrice = <?php echo $basePrice; ?>;
        const displayTotal = document.getElementById('display-total');
        const finalPriceInput = document.getElementById('final-price-input');
        const finalDescInput = document.getElementById('final-desc');

        function updatedTotal() {
            let total = initialBasePrice;
            
            // Radio base flavor
            const selectedBase = document.querySelector('input[name="base_flavor"]:checked');
            if(selectedBase) {
                total += parseFloat(selectedBase.getAttribute('data-price') || 0);
            }

            // Checkboxes
            document.querySelectorAll('input[type="checkbox"]:checked').forEach(cb => {
                total += parseFloat(cb.getAttribute('data-price') || 0);
            });

            displayTotal.textContent = 'R$ ' + total.toFixed(2).replace('.', ',');
            finalPriceInput.value = total.toFixed(2);
        }

        // Attach listeners
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('change', updatedTotal);
        });

        function prepareSubmit(e) {
            e.preventDefault();
            
            // Gather description
            let descParts = [];
            
            const base = document.querySelector('input[name="base_flavor"]:checked');
            if(base) descParts.push(`Base: ${base.value}`);

            let fruits = [];
            document.querySelectorAll('input[name="fruits[]"]:checked').forEach(el => fruits.push(el.value));
            if(fruits.length > 0) descParts.push(`Frutas: ${fruits.join(', ')}`);

            let toppings = [];
            document.querySelectorAll('input[name="free_toppings[]"]:checked').forEach(el => toppings.push(el.value));
            if(toppings.length > 0) descParts.push(`Grátis: ${toppings.join(', ')}`);

            let premiums = [];
            document.querySelectorAll('input[name="premium[]"]:checked').forEach(el => premiums.push(el.value));
            if(premiums.length > 0) descParts.push(`Premium: ${premiums.join(', ')}`);
            
            let caldas = [];
            document.querySelectorAll('input[name="caldas[]"]:checked').forEach(el => caldas.push(el.value));
            if(caldas.length > 0) descParts.push(`Caldas: ${caldas.join(', ')}`);

            finalDescInput.value = descParts.join(' | ');
            
            document.getElementById('customize-form').submit();
        }
    </script>
</body>
</html>
