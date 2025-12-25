import Head from 'next/head';
import Link from 'next/link';
import { products } from '@/data/products';
import styles from '@/styles/Home.module.css';

export default function Home() {
    return (
        <>
            <Head>
                <title>Zé do Açaí - Delivery</title>
                <meta name="description" content="O melhor açaí da região" />
                <meta name="viewport" content="width=device-width, initial-scale=1" />
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
            </Head>

            <div className={styles.container}>
                {/* Header */}
                <header className={styles.header}>
                    <div className={styles.info}>
                        <h1 className={styles.storeName}>
                            Zé do Açaí
                            <i className="fa-solid fa-circle-check" style={{ fontSize: '16px', marginLeft: '5px' }}></i>
                        </h1>
                        <div className={styles.rating}>
                            <span>⭐ 4.9</span>
                            <span>• 30-40 min</span>
                            <span>• 📍 Centro</span>
                        </div>
                        <div className={styles.status}>
                            <span className={styles.statusOpen}>🟢 ABERTO</span>
                        </div>
                    </div>
                </header>

                {/* Products Section */}
                <main className={styles.main}>
                    <section className={styles.category}>
                        <h2>🔥 Promoções</h2>
                        <div className={styles.produtos}>
                            {products.map((product) => (
                                <div key={product.id} className={styles.item}>
                                    <Link
                                        href={{
                                            pathname: '/customize',
                                            query: {
                                                productId: product.id,
                                                name: product.name,
                                                price: product.price,
                                                image: product.image,
                                            },
                                        }}
                                        className={styles.productLink}
                                    >
                                        <div className={styles.texto}>
                                            <h3>
                                                {product.name} <b>({Math.round(((product.oldPrice! - product.price) / product.oldPrice!) * 100)}% OFF)</b>
                                            </h3>
                                            <span>{product.description}</span>
                                            {product.oldPrice && (
                                                <>
                                                    {' de '}
                                                    <span className={styles.precoPromocao}>
                                                        R$ {product.oldPrice.toFixed(2).replace('.', ',')}
                                                    </span>
                                                    {' por'}
                                                </>
                                            )}
                                            <br />
                                            <span className={styles.preco}>
                                                R$ {product.price.toFixed(2).replace('.', ',')}
                                            </span>
                                            <br />
                                            <span className={styles.estoque}>
                                                {product.id === 'acai-500ml' ? '🔥 Mais Vendido!' : '⚡ Sai muito!'}
                                            </span>
                                        </div>
                                        <div className={styles.fotoProduto}>
                                            <figure>
                                                <img
                                                    src={product.image}
                                                    alt={product.name}
                                                    width={110}
                                                    height={110}
                                                    loading="lazy"
                                                />
                                            </figure>
                                        </div>
                                    </Link>
                                </div>
                            ))}
                        </div>
                    </section>
                </main>

                {/* Footer */}
                <footer className={styles.footer}>
                    <p>© 2025 Zé do Açaí - Todos os direitos reservados</p>
                </footer>
            </div>
        </>
    );
}
