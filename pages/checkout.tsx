import { useState } from 'react';
import { useRouter } from 'next/router';
import Head from 'next/head';
import styles from '@/styles/Checkout.module.css';

export default function Checkout() {
    const router = useRouter();
    const { productName, productPrice, productImage, description } = router.query;

    const [step, setStep] = useState(1);
    const [formData, setFormData] = useState({
        name: '',
        email: '',
        phone: '',
        cpf: '',
        cep: '',
        street: '',
        number: '',
        complement: '',
        neighborhood: '',
        city: '',
        uf: '',
    });

    const [upsells, setUpsells] = useState({ water: false, brownie: false });
    const [total, setTotal] = useState(parseFloat(productPrice as string) || 0);

    const basePrice = parseFloat(productPrice as string) || 0;

    const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        setFormData({ ...formData, [e.target.name]: e.target.value });
    };

    const validateStep1 = () => {
        if (!formData.name || !formData.email || !formData.phone || !formData.cpf) {
            alert('Por favor, preencha todos os campos pessoais.');
            return false;
        }
        return true;
    };

    const validateStep2 = () => {
        if (!formData.cep || !formData.street || !formData.number || !formData.city) {
            alert('Por favor, preencha todos os campos de endereço.');
            return false;
        }
        return true;
    };

    const searchCep = async () => {
        const cep = formData.cep.replace(/\D/g, '');
        if (cep.length === 8) {
            try {
                const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
                const data = await response.json();
                if (!data.erro) {
                    setFormData({
                        ...formData,
                        street: data.logradouro,
                        neighborhood: data.bairro,
                        city: data.localidade,
                        uf: data.uf,
                    });
                } else {
                    alert('CEP não encontrado.');
                }
            } catch (error) {
                alert('Erro ao buscar CEP.');
            }
        }
    };

    const toggleUpsell = (type: 'water' | 'brownie', price: number) => {
        const newUpsells = { ...upsells, [type]: !upsells[type] };
        setUpsells(newUpsells);

        let newTotal = basePrice;
        if (newUpsells.water) newTotal += 3.0;
        if (newUpsells.brownie) newTotal += 6.0;
        setTotal(newTotal);
    };

    const handleSubmit = async () => {
        if (!validateStep2()) return;

        router.push({
            pathname: '/payment',
            query: {
                ...formData,
                productName,
                productPrice: total.toFixed(2),
                upsellWater: upsells.water ? '1' : '0',
                upsellBrownie: upsells.brownie ? '1' : '0',
                description,
            },
        });
    };

    return (
        <>
            <Head>
                <title>Checkout - Zé do Açaí</title>
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
            </Head>

            <div className={styles.container}>
                <div className={styles.header}>
                    <h1>Finalizar Pedido</h1>
                </div>

                {/* Product Summary */}
                <div className={styles.productCard}>
                    <img src={productImage as string} alt={productName as string} className={styles.productImg} />
                    <div className={styles.productInfo}>
                        <h3>{productName}</h3>
                        {description && <p className={styles.description}>{description}</p>}
                        <div className={styles.price}>R$ {basePrice.toFixed(2).replace('.', ',')}</div>
                    </div>
                </div>

                {/* Step 1: Personal Data */}
                {step === 1 && (
                    <div className={styles.step}>
                        <h2>1. Dados Pessoais</h2>
                        <input
                            type="text"
                            name="name"
                            placeholder="Nome completo"
                            value={formData.name}
                            onChange={handleInputChange}
                            className={styles.input}
                        />
                        <input
                            type="email"
                            name="email"
                            placeholder="E-mail"
                            value={formData.email}
                            onChange={handleInputChange}
                            className={styles.input}
                        />
                        <input
                            type="tel"
                            name="phone"
                            placeholder="Telefone"
                            value={formData.phone}
                            onChange={handleInputChange}
                            className={styles.input}
                        />
                        <input
                            type="text"
                            name="cpf"
                            placeholder="CPF"
                            value={formData.cpf}
                            onChange={handleInputChange}
                            className={styles.input}
                        />
                        <button
                            className={styles.btnNext}
                            onClick={() => {
                                if (validateStep1()) setStep(2);
                            }}
                        >
                            Continuar
                        </button>
                    </div>
                )}

                {/* Step 2: Address */}
                {step === 2 && (
                    <div className={styles.step}>
                        <h2>2. Endereço de Entrega</h2>
                        <div className={styles.cepRow}>
                            <input
                                type="text"
                                name="cep"
                                placeholder="CEP"
                                value={formData.cep}
                                onChange={handleInputChange}
                                className={styles.input}
                            />
                            <button className={styles.btnSearch} onClick={searchCep}>
                                Buscar
                            </button>
                        </div>
                        <input
                            type="text"
                            name="street"
                            placeholder="Rua"
                            value={formData.street}
                            onChange={handleInputChange}
                            className={styles.input}
                        />
                        <input
                            type="text"
                            name="number"
                            placeholder="Número"
                            value={formData.number}
                            onChange={handleInputChange}
                            className={styles.input}
                        />
                        <input
                            type="text"
                            name="complement"
                            placeholder="Complemento (opcional)"
                            value={formData.complement}
                            onChange={handleInputChange}
                            className={styles.input}
                        />
                        <input
                            type="text"
                            name="neighborhood"
                            placeholder="Bairro"
                            value={formData.neighborhood}
                            onChange={handleInputChange}
                            className={styles.input}
                        />
                        <input
                            type="text"
                            name="city"
                            placeholder="Cidade"
                            value={formData.city}
                            onChange={handleInputChange}
                            className={styles.input}
                        />
                        <input
                            type="text"
                            name="uf"
                            placeholder="UF"
                            value={formData.uf}
                            onChange={handleInputChange}
                            className={styles.input}
                        />
                        <button className={styles.btnNext} onClick={() => setStep(3)}>
                            Continuar
                        </button>
                    </div>
                )}

                {/* Step 3: Upsells */}
                {step === 3 && (
                    <div className={styles.step}>
                        <h2>3. Adicione ao seu pedido</h2>
                        <div className={styles.upsellCard} onClick={() => toggleUpsell('water', 3.0)}>
                            <div className={styles.upsellIcon}>💧</div>
                            <div className={styles.upsellInfo}>
                                <b>Água Mineral 500ml</b>
                                <div className={styles.upsellPrice}>R$ 3,00</div>
                            </div>
                            <div className={`${styles.checkbox} ${upsells.water ? styles.checked : ''}`}>
                                {upsells.water && <i className="fa-solid fa-check"></i>}
                            </div>
                        </div>

                        <div className={styles.upsellCard} onClick={() => toggleUpsell('brownie', 6.0)}>
                            <div className={styles.upsellIcon}>🍫</div>
                            <div className={styles.upsellInfo}>
                                <b>Brownie de Chocolate</b>
                                <div className={styles.upsellPrice}>R$ 6,00</div>
                            </div>
                            <div className={`${styles.checkbox} ${upsells.brownie ? styles.checked : ''}`}>
                                {upsells.brownie && <i className="fa-solid fa-check"></i>}
                            </div>
                        </div>

                        <div className={styles.totalSection}>
                            <div className={styles.totalLabel}>Total:</div>
                            <div className={styles.totalValue}>R$ {total.toFixed(2).replace('.', ',')}</div>
                        </div>

                        <button className={styles.btnFinish} onClick={handleSubmit}>
                            Ir para Pagamento
                        </button>
                    </div>
                )}
            </div>
        </>
    );
}
