import { useState } from 'react';
import { useRouter } from 'next/router';
import Head from 'next/head';
import { estados, cidadesPorEstado } from '@/data/locations';
import styles from '@/styles/Location.module.css';

type Step = 'selection' | 'loading' | 'success';

export default function Location() {
    const router = useRouter();
    const [step, setStep] = useState<Step>('selection');
    const [selectedEstado, setSelectedEstado] = useState('');
    const [selectedCidade, setSelectedCidade] = useState('');
    const [distance, setDistance] = useState('');

    const handleNext = () => {
        if (!selectedEstado || !selectedCidade) return;

        setStep('loading');

        // Simula busca de loja (2-3 segundos)
        setTimeout(() => {
            // Distância aleatória entre 1-5km
            const randomDistance = (Math.random() * 4 + 1).toFixed(2);
            setDistance(randomDistance);
            setStep('success');
        }, 2500);
    };

    const handleGoToMenu = () => {
        // Salva localização no localStorage
        localStorage.setItem('userLocation', JSON.stringify({
            estado: selectedEstado,
            cidade: selectedCidade,
            distance: distance
        }));

        // Redireciona para o menu
        router.push('/menu');
    };

    const estadoNome = estados.find(e => e.uf === selectedEstado)?.nome || '';
    const cidades = selectedEstado ? cidadesPorEstado[selectedEstado] || [] : [];

    return (
        <>
            <Head>
                <title>Selecione sua localização - Zé do Açaí</title>
            </Head>

            <div className={styles.overlay}>
                {step === 'selection' && (
                    <div className={styles.modal}>
                        <h1 className={styles.title}>
                            Procure a loja mais <span className={styles.highlightRed}>próxima</span> de você!
                        </h1>
                        <p className={styles.subtitle}>Escolha seu estado:</p>

                        <div className={styles.formGroup}>
                            <select
                                className={styles.select}
                                value={selectedEstado}
                                onChange={(e) => {
                                    setSelectedEstado(e.target.value);
                                    setSelectedCidade(''); // Reset cidade
                                }}
                            >
                                <option value="">Selecione um estado</option>
                                {estados.map((estado) => (
                                    <option key={estado.uf} value={estado.uf}>
                                        {estado.nome}
                                    </option>
                                ))}
                            </select>
                        </div>

                        {selectedEstado && (
                            <>
                                <p className={styles.subtitle}>Escolha sua cidade:</p>
                                <div className={styles.formGroup}>
                                    <select
                                        className={styles.select}
                                        value={selectedCidade}
                                        onChange={(e) => setSelectedCidade(e.target.value)}
                                    >
                                        <option value="">Selecione uma cidade</option>
                                        {cidades.map((cidade) => (
                                            <option key={cidade} value={cidade}>
                                                {cidade}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                            </>
                        )}

                        <button
                            className={styles.btnPrimary}
                            onClick={handleNext}
                            disabled={!selectedEstado || !selectedCidade}
                        >
                            Próximo
                        </button>
                    </div>
                )}

                {step === 'loading' && (
                    <div className={styles.modal}>
                        <div className={styles.loadingContainer}>
                            <h2 className={styles.loadingTitle}>Procurando a loja mais próxima...</h2>
                            <p className={styles.loadingText}>
                                Procurando a loja mais próxima de você em {selectedCidade}...
                            </p>
                            <div className={styles.spinner}></div>
                        </div>
                    </div>
                )}

                {step === 'success' && (
                    <div className={styles.modal}>
                        <div className={styles.successContainer}>
                            <div className={styles.checkIcon}></div>
                            <p className={styles.successText}>
                                A loja mais próxima fica a <span className={styles.distance}>{distance}km</span> de você!<br />
                                Seu pedido chegará entre 15 a 45 minutos.
                            </p>
                            <button className={styles.btnSuccess} onClick={handleGoToMenu}>
                                Olhar cardápio de ofertas!
                            </button>
                        </div>
                    </div>
                )}
            </div>
        </>
    );
}
