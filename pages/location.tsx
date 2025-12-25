import { useState, useEffect } from 'react';
import { useRouter } from 'next/router';
import Head from 'next/head';
import { estados, cidadesPorEstado } from '@/data/locations';
import styles from '@/styles/Location.module.css';

type Step = 'detecting' | 'selection' | 'loading' | 'success';

export default function Location() {
    const router = useRouter();
    const [step, setStep] = useState<Step>('detecting');
    const [selectedEstado, setSelectedEstado] = useState('');
    const [selectedCidade, setSelectedCidade] = useState('');
    const [distance, setDistance] = useState('');
    const [detectedLocation, setDetectedLocation] = useState('');

    // Detecta localização automaticamente ao carregar
    useEffect(() => {
        detectLocation();
    }, []);

    const detectLocation = async () => {
        try {
            // Usa API de geolocalização por IP (não precisa de permissão)
            const response = await fetch('https://ipapi.co/json/');
            const data = await response.json();

            // Extrai estado e cidade do IP
            const estado = data.region || '';
            const cidade = data.city || '';

            // Encontra o UF do estado
            const estadoObj = estados.find(e =>
                e.nome.toLowerCase().includes(estado.toLowerCase()) ||
                estado.toLowerCase().includes(e.nome.toLowerCase())
            );

            if (estadoObj && cidade) {
                setSelectedEstado(estadoObj.uf);
                setSelectedCidade(cidade);
                setDetectedLocation(`${cidade}, ${estadoObj.nome}`);

                // Vai direto para loading após 1.5s
                setTimeout(() => {
                    handleAutoSearch(estadoObj.uf, cidade);
                }, 1500);
            } else {
                // Se não conseguiu detectar, mostra seleção manual
                setStep('selection');
            }
        } catch (error) {
            console.error('Erro ao detectar localização:', error);
            // Se erro, mostra seleção manual
            setStep('selection');
        }
    };

    const handleAutoSearch = (uf: string, cidade: string) => {
        setStep('loading');

        // Simula busca de loja (2-3 segundos)
        setTimeout(() => {
            const randomDistance = (Math.random() * 4 + 1).toFixed(2);
            setDistance(randomDistance);
            setStep('success');
        }, 2500);
    };

    const handleManualNext = () => {
        if (!selectedEstado || !selectedCidade) return;

        setStep('loading');

        setTimeout(() => {
            const randomDistance = (Math.random() * 4 + 1).toFixed(2);
            setDistance(randomDistance);
            setStep('success');
        }, 2500);
    };

    const handleGoToMenu = () => {
        localStorage.setItem('userLocation', JSON.stringify({
            estado: selectedEstado,
            cidade: selectedCidade,
            distance: distance
        }));

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
                {step === 'detecting' && (
                    <div className={styles.modal}>
                        <div className={styles.loadingContainer}>
                            <h2 className={styles.loadingTitle}>Detectando sua localização...</h2>
                            <p className={styles.loadingText}>
                                Aguarde enquanto identificamos sua localização
                            </p>
                            <div className={styles.spinner}></div>
                        </div>
                    </div>
                )}

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
                                    setSelectedCidade('');
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
                            onClick={handleManualNext}
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
                                Procurando a loja mais próxima de você em {detectedLocation || selectedCidade}...
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

