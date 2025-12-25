import { useEffect } from 'react';
import { useRouter } from 'next/router';

export default function Home() {
    const router = useRouter();

    useEffect(() => {
        // Verifica se já tem localização salva
        const savedLocation = localStorage.getItem('userLocation');

        if (savedLocation) {
            // Se já selecionou, vai direto pro menu
            router.replace('/menu');
        } else {
            // Se não, vai para seleção de localização
            router.replace('/location');
        }
    }, [router]);

    return (
        <div style={{
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
            height: '100vh',
            background: '#ffcc00'
        }}>
            <div style={{ textAlign: 'center' }}>
                <h1 style={{ color: '#6f2c91', fontSize: '2rem' }}>Zé do Açaí</h1>
                <p>Carregando...</p>
            </div>
        </div>
    );
}
