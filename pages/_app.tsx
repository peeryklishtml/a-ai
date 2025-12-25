import type { AppProps } from 'next/app';
import { OrderProvider } from '@/context/OrderContext';
import '@/styles/globals.css';

export default function App({ Component, pageProps }: AppProps) {
    return (
        <OrderProvider>
            <Component {...pageProps} />
        </OrderProvider>
    );
}
