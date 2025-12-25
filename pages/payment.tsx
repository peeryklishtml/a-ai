import { useState, useEffect } from 'react';
import { useRouter } from 'next/router';
import Head from 'next/head';
import { QRCodeSVG } from 'qrcode.react';
import styles from '@/styles/Payment.module.css';

export default function Payment() {
    const router = useRouter();
    const {
        name,
        email,
        cpf,
        phone,
        cep,
        street,
        number,
        complement,
        neighborhood,
        city,
        uf,
        productName,
        productPrice,
        upsellWater,
        upsellBrownie,
        description,
    } = router.query;

    const [qrCodeText, setQrCodeText] = useState('');
    const [transactionId, setTransactionId] = useState('');
    const [loading, setLoading] = useState(true);
    const [timer, setTimer] = useState(1200); // 20 minutes (PixGo expiration time)

    const totalAmount = parseFloat(productPrice as string) || 0;

    useEffect(() => {
        // Create payment
        const createPayment = async () => {
            try {
                // Build complete address string
                const fullAddress = `${street}, ${number}${complement ? ', ' + complement : ''}, ${neighborhood}, ${city}, ${uf}, ${cep}`;

                const response = await fetch('/api/create-payment', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        amount: totalAmount,
                        name,
                        email,
                        cpf,
                        phone,
                        address: fullAddress,
                        productName,
                        externalId: `acai_${Date.now()}`,
                    }),
                });

                const data = await response.json();

                if (data.success && data.qrCodeText && data.transactionId) {
                    setQrCodeText(data.qrCodeText);
                    setTransactionId(data.transactionId);

                    // Optional: Use qr_image_url if you prefer to display image instead of generating
                    // setQrImageUrl(data.qrImageUrl);
                } else {
                    console.error('Payment creation failed:', data);
                    alert('Erro ao gerar pagamento: ' + (data.message || 'Tente novamente'));
                }
            } catch (error) {
                console.error('Error creating payment:', error);
                alert('Erro ao conectar com o servidor de pagamento');
            } finally {
                setLoading(false);
            }
        };

        if (router.isReady) {
            createPayment();
        }
    }, [router.isReady, totalAmount, name, email, cpf, phone, street, number, complement, neighborhood, city, uf, cep, productName]);

    // Timer countdown (20 minutes for PixGo)
    useEffect(() => {
        const interval = setInterval(() => {
            setTimer((prev) => (prev > 0 ? prev - 1 : 0));
        }, 1000);
        return () => clearInterval(interval);
    }, []);

    // Check payment status every 2 minutes
    useEffect(() => {
        if (!transactionId) return;

        const checkStatus = async () => {
            try {
                const response = await fetch(`/api/check-payment-status?paymentId=${transactionId}`);
                const data = await response.json();

                if (data.success) {
                    if (data.status === 'completed') {
                        // Payment confirmed!
                        alert('✅ Pagamento confirmado! Obrigado pela compra.');
                        // You can redirect to a success page here
                        // router.push('/success');
                    } else if (data.status === 'expired') {
                        alert('⏰ Pagamento expirado. Por favor, faça um novo pedido.');
                    } else if (data.status === 'cancelled') {
                        alert('❌ Pagamento cancelado.');
                    }
                }
            } catch (error) {
                console.error('Error checking payment status:', error);
            }
        };

        // Check immediately after 30 seconds
        const initialTimeout = setTimeout(checkStatus, 30000);

        // Then check every 2 minutes (120000ms)
        const statusInterval = setInterval(checkStatus, 120000);

        // Cleanup on unmount
        return () => {
            clearTimeout(initialTimeout);
            clearInterval(statusInterval);
        };
    }, [transactionId]);

    const minutes = Math.floor(timer / 60);
    const seconds = timer % 60;

    const copyPix = () => {
        navigator.clipboard.writeText(qrCodeText);
        alert('Código Pix copiado com sucesso!');
    };

    if (loading) {
        return (
            <div className={styles.loading}>
                <div className={styles.spinner}></div>
                <p>Gerando QR Code...</p>
            </div>
        );
    }

    return (
        <>
            <Head>
                <title>Pagamento Pix - Zé do Açaí</title>
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
            </Head>

            <div className={styles.container}>
                <div className={styles.ticketContainer}>
                    <div className={styles.header}>
                        <div className={styles.storeHeader}>
                            <span className={styles.storeName}>Loja 01</span>
                            <i className="fa-solid fa-shapes" style={{ color: '#9aa', fontSize: '1.2em' }}></i>
                        </div>

                        <div className={styles.titleRow}>
                            <h1 className={styles.pageTitle}>Pagamento via Pix</h1>
                            <div className={styles.dateInfo}>
                                <div>
                                    <i className="fa-regular fa-calendar"></i> {new Date().toLocaleDateString('pt-BR')}
                                </div>
                                <div>ID {transactionId.substring(0, 10).toUpperCase()}</div>
                            </div>
                        </div>

                        <div className={styles.productName}>{productName}</div>

                        <div className={styles.customerGrid}>
                            <div className={styles.customerCol}>
                                <div className={styles.infoRow}>
                                    <i className="fa-regular fa-envelope"></i>
                                    <span>{email}</span>
                                </div>
                                <div className={styles.infoRow}>
                                    <i className="fa-regular fa-id-card"></i>
                                    <span>{cpf}</span>
                                </div>
                            </div>
                            <div className={styles.customerCol}>
                                <div className={styles.infoRow}>
                                    <i className="fa-solid fa-location-dot"></i>
                                    <div className={styles.addressText}>
                                        <div>CEP {cep}</div>
                                        <div>{street}</div>
                                        <div>
                                            n. {number}, {neighborhood}
                                        </div>
                                        <div>
                                            {city} - {uf}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className={styles.priceSection}>
                        <div>
                            <div className={styles.totalLabel}>Total</div>
                            <div className={styles.priceContainer}>
                                <span className={styles.priceSymbol}>R$</span>
                                <span className={styles.priceVal}>{totalAmount.toFixed(2).replace('.', ',')}</span>
                            </div>
                        </div>
                        <div className={styles.timerWrapper}>
                            <div className={styles.timerLabel}>Expira em</div>
                            <div className={styles.timerDigits}>
                                <i className="fa-regular fa-clock"></i>
                                <span className={styles.digitBox}>{String(minutes).padStart(2, '0')}</span>:
                                <span className={styles.digitBox}>{String(seconds).padStart(2, '0')}</span>
                            </div>
                        </div>
                    </div>

                    <div className={styles.qrArea}>
                        <div className={styles.qrInstruction}>Escaneie o QR CODE ou copie o código</div>
                        <div className={styles.qrBox}>
                            <QRCodeSVG value={qrCodeText} size={180} />
                        </div>

                        <input type="text" className={styles.copyField} value={qrCodeText} readOnly onClick={(e) => e.currentTarget.select()} />

                        <button className={styles.btnYellow} onClick={copyPix}>
                            Copiar código <i className="fa-regular fa-copy"></i>
                        </button>
                    </div>

                    <div className={styles.warningBox}>
                        <i className="fa-solid fa-circle-info"></i>
                        <div className={styles.warningText}>
                            <b>Atenção:</b>
                            <br />
                            Os bancos reforçaram a segurança do Pix e podem exibir avisos preventivos.
                            <br />
                            Não se preocupe, sua transação está protegida.
                        </div>
                    </div>

                    <div className={styles.stepsContainer}>
                        <div className={styles.stepsIntro}>Para finalizar sua compra, compense o Pix no prazo limite.</div>

                        <div className={styles.stepRow}>
                            <div className={styles.stepBadge}>PASSO 1</div>
                            <div className={styles.stepDesc}>Abra o app do seu banco e entre no ambiente Pix;</div>
                        </div>
                        <div className={styles.stepRow}>
                            <div className={styles.stepBadge}>PASSO 2</div>
                            <div className={styles.stepDesc}>Escolha Pagar com QR Code e aponte a câmera para o código acima, ou cole o código identificador da transação;</div>
                        </div>
                        <div className={styles.stepRow}>
                            <div className={styles.stepBadge}>PASSO 3</div>
                            <div className={styles.stepDesc}>Confirme as informações e finalize sua compra.</div>
                        </div>
                    </div>

                    <div className={styles.summaryFooter}>
                        <div className={styles.summaryItem}>
                            <span>{productName}</span>
                            <span className={styles.summaryPrice}>R$ {parseFloat(productPrice as string).toFixed(2).replace('.', ',')}</span>
                        </div>

                        {upsellWater === '1' && (
                            <div className={styles.summaryItem}>
                                <span>Água Mineral 500ml (x1)</span>
                                <span className={styles.summaryPrice}>R$ 3,00</span>
                            </div>
                        )}

                        {upsellBrownie === '1' && (
                            <div className={styles.summaryItem}>
                                <span>Brownie de Chocolate (x1)</span>
                                <span className={styles.summaryPrice}>R$ 6,00</span>
                            </div>
                        )}

                        <div className={styles.summaryItem}>
                            <span>Frete</span>
                            <span className={styles.freteTag}>GRÁTIS</span>
                        </div>

                        <div className={`${styles.summaryItem} ${styles.total}`}>
                            <span>Total</span>
                            <span>R$ {totalAmount.toFixed(2).replace('.', ',')}</span>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
