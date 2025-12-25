import { useState, useEffect } from 'react';
import { useRouter } from 'next/router';
import Head from 'next/head';
import Link from 'next/link';
import { customizationOptions } from '@/data/products';
import { CustomizationOption } from '@/types';
import styles from '@/styles/Customize.module.css';

export default function Customize() {
    const router = useRouter();
    const { productId, name, price, image } = router.query;

    const [selectedBase, setSelectedBase] = useState<CustomizationOption | null>(null);
    const [selectedFruits, setSelectedFruits] = useState<CustomizationOption[]>([]);
    const [selectedFreeToppings, setSelectedFreeToppings] = useState<CustomizationOption[]>([]);
    const [selectedPremium, setSelectedPremium] = useState<CustomizationOption[]>([]);
    const [selectedCaldas, setSelectedCaldas] = useState<CustomizationOption[]>([]);
    const [total, setTotal] = useState(0);

    const basePrice = parseFloat(price as string) || 0;

    // Calculate total
    useEffect(() => {
        let sum = basePrice;
        if (selectedBase) sum += selectedBase.price;
        selectedFruits.forEach((f) => (sum += f.price));
        selectedFreeToppings.forEach((t) => (sum += t.price));
        selectedPremium.forEach((p) => (sum += p.price));
        selectedCaldas.forEach((c) => (sum += c.price));
        setTotal(sum);
    }, [selectedBase, selectedFruits, selectedFreeToppings, selectedPremium, selectedCaldas, basePrice]);

    const handleFinish = () => {
        const description = [
            selectedBase ? `Base: ${selectedBase.name}` : '',
            selectedFruits.length > 0 ? `Frutas: ${selectedFruits.map((f) => f.name).join(', ')}` : '',
            selectedFreeToppings.length > 0 ? `Grátis: ${selectedFreeToppings.map((t) => t.name).join(', ')}` : '',
            selectedPremium.length > 0 ? `Premium: ${selectedPremium.map((p) => p.name).join(', ')}` : '',
            selectedCaldas.length > 0 ? `Caldas: ${selectedCaldas.map((c) => c.name).join(', ')}` : '',
        ]
            .filter(Boolean)
            .join(' | ');

        router.push({
            pathname: '/checkout',
            query: {
                productName: name,
                productPrice: total.toFixed(2),
                productImage: image,
                description,
            },
        });
    };

    const toggleFruit = (fruit: CustomizationOption) => {
        if (selectedFruits.find((f) => f.id === fruit.id)) {
            setSelectedFruits(selectedFruits.filter((f) => f.id !== fruit.id));
        } else if (selectedFruits.length < 3) {
            setSelectedFruits([...selectedFruits, fruit]);
        }
    };

    const toggleTopping = (topping: CustomizationOption, category: 'free_toppings' | 'premium' | 'caldas') => {
        const setter = category === 'free_toppings' ? setSelectedFreeToppings : category === 'premium' ? setSelectedPremium : setSelectedCaldas;
        const selected = category === 'free_toppings' ? selectedFreeToppings : category === 'premium' ? selectedPremium : selectedCaldas;

        if (selected.find((t) => t.id === topping.id)) {
            setter(selected.filter((t) => t.id !== topping.id));
        } else {
            setter([...selected, topping]);
        }
    };

    return (
        <>
            <Head>
                <title>Monte seu Açaí - Zé do Açaí</title>
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
            </Head>

            <div className={styles.container}>
                <div className={styles.header}>
                    <Link href="/" className={styles.backBtn}>
                        <i className="fa-solid fa-arrow-left"></i>
                    </Link>
                    <h1>Monte seu Açaí</h1>
                </div>

                <div className={styles.productSummary}>
                    <img src={image as string} alt={name as string} />
                    <h2>{name}</h2>
                    <p>Base: R$ {basePrice.toFixed(2).replace('.', ',')}</p>
                </div>

                {/* Base Flavor */}
                <div className={styles.section}>
                    <div className={styles.sectionTitle}>
                        1. Escolha a Base <i className="fa-solid fa-ice-cream"></i>
                    </div>
                    <div className={styles.sectionSubtitle}>Selecione 1 opção (Obrigatório)</div>
                    <div className={styles.optionGroup}>
                        {customizationOptions
                            .filter((opt) => opt.category === 'base')
                            .map((opt) => (
                                <label key={opt.id} className={styles.optionItem}>
                                    <input
                                        type="radio"
                                        name="base"
                                        checked={selectedBase?.id === opt.id}
                                        onChange={() => setSelectedBase(opt)}
                                    />
                                    <div className={styles.optionDetails}>
                                        <span className={styles.optionName}>{opt.name}</span>
                                        {opt.price > 0 && <div className={styles.optionPrice}>+ R$ {opt.price.toFixed(2).replace('.', ',')}</div>}
                                    </div>
                                </label>
                            ))}
                    </div>
                </div>

                {/* Fruits */}
                <div className={styles.section}>
                    <div className={styles.sectionTitle}>
                        2. Frutas <i className="fa-solid fa-lemon"></i>
                    </div>
                    <div className={styles.sectionSubtitle}>Escolha até 3 opções</div>
                    <div className={styles.optionGroup}>
                        {customizationOptions
                            .filter((opt) => opt.category === 'fruits')
                            .map((opt) => (
                                <label key={opt.id} className={styles.optionItem}>
                                    <input
                                        type="checkbox"
                                        checked={!!selectedFruits.find((f) => f.id === opt.id)}
                                        onChange={() => toggleFruit(opt)}
                                    />
                                    <span className={styles.optionName}>{opt.name}</span>
                                </label>
                            ))}
                    </div>
                </div>

                {/* Free Toppings */}
                <div className={styles.section}>
                    <div className={styles.sectionTitle}>
                        3. Complementos Grátis <i className="fa-solid fa-spoon"></i>
                    </div>
                    <div className={styles.sectionSubtitle}>Escolha à vontade</div>
                    <div className={styles.optionGroup}>
                        {customizationOptions
                            .filter((opt) => opt.category === 'free_toppings')
                            .map((opt) => (
                                <label key={opt.id} className={styles.optionItem}>
                                    <input
                                        type="checkbox"
                                        checked={!!selectedFreeToppings.find((t) => t.id === opt.id)}
                                        onChange={() => toggleTopping(opt, 'free_toppings')}
                                    />
                                    <span className={styles.optionName}>{opt.name}</span>
                                </label>
                            ))}
                    </div>
                </div>

                {/* Premium */}
                <div className={styles.section}>
                    <div className={styles.sectionTitle}>
                        4. Turbinar (Premium) <i className="fa-solid fa-bolt"></i>
                    </div>
                    <div className={styles.sectionSubtitle}>Adicione sabor extra (Pago)</div>
                    <div className={styles.optionGroup}>
                        {customizationOptions
                            .filter((opt) => opt.category === 'premium')
                            .map((opt) => (
                                <label key={opt.id} className={styles.optionItem}>
                                    <input
                                        type="checkbox"
                                        checked={!!selectedPremium.find((p) => p.id === opt.id)}
                                        onChange={() => toggleTopping(opt, 'premium')}
                                    />
                                    <div className={styles.optionDetails}>
                                        <span className={styles.optionName}>{opt.name}</span>
                                        <div className={styles.optionPrice}>+ R$ {opt.price.toFixed(2).replace('.', ',')}</div>
                                    </div>
                                </label>
                            ))}
                    </div>
                </div>

                {/* Caldas */}
                <div className={styles.section}>
                    <div className={styles.sectionTitle}>
                        5. Caldas <i className="fa-solid fa-fill-drip"></i>
                    </div>
                    <div className={styles.optionGroup}>
                        {customizationOptions
                            .filter((opt) => opt.category === 'caldas')
                            .map((opt) => (
                                <label key={opt.id} className={styles.optionItem}>
                                    <input
                                        type="checkbox"
                                        checked={!!selectedCaldas.find((c) => c.id === opt.id)}
                                        onChange={() => toggleTopping(opt, 'caldas')}
                                    />
                                    <span className={styles.optionName}>{opt.name}</span>
                                </label>
                            ))}
                    </div>
                </div>

                {/* Footer */}
                <div className={styles.footerBar}>
                    <div className={styles.totalPrice}>
                        <small>Total do Pedido:</small>
                        <span>R$ {total.toFixed(2).replace('.', ',')}</span>
                    </div>
                    <button className={styles.btnFinish} onClick={handleFinish} disabled={!selectedBase}>
                        Finalizar <i className="fa-solid fa-check"></i>
                    </button>
                </div>
            </div>
        </>
    );
}
