/** @type {import('next').NextConfig} */
const nextConfig = {
    reactStrictMode: true,
    images: {
        domains: ['media.istockphoto.com', 'p2.trrsf.com'],
    },
    // Netlify compatibility
    output: 'standalone',
}

module.exports = nextConfig
