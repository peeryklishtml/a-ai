import type { NextApiRequest, NextApiResponse } from 'next';

// PixGo API Configuration
const PIXGO_API_KEY = 'pk_1234567890abcdef1234567890abcdef1234567890abcdef1234567890abcdef';
const PIXGO_BASE_URL = 'https://pixgo.org/api/v1';

export default async function handler(req: NextApiRequest, res: NextApiResponse) {
    if (req.method !== 'POST') {
        return res.status(405).json({ error: 'Method not allowed' });
    }

    const { amount, name, email, cpf, phone, address, productName, externalId } = req.body;

    // Validate required fields
    if (!amount || amount < 10) {
        return res.status(400).json({ error: 'Amount must be at least R$ 10.00' });
    }

    try {
        // Create payment with PixGo API
        const response = await fetch(`${PIXGO_BASE_URL}/payment/create`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-API-Key': PIXGO_API_KEY,
            },
            body: JSON.stringify({
                amount: parseFloat(amount),
                description: productName || 'Pedido Zé do Açaí',
                customer_name: name,
                customer_cpf: cpf?.replace(/\D/g, ''),
                customer_email: email,
                customer_phone: phone,
                customer_address: address,
                external_id: externalId || `order_${Date.now()}`,
            }),
        });

        const data = await response.json();

        if (response.ok && data.success) {
            // Success - return payment data
            return res.status(201).json({
                success: true,
                qrCodeText: data.data.qr_code,
                qrImageUrl: data.data.qr_image_url,
                transactionId: data.data.payment_id,
                externalId: data.data.external_id,
                amount: data.data.amount,
                status: data.data.status,
                expiresAt: data.data.expires_at,
                createdAt: data.data.created_at,
            });
        } else {
            // Error from PixGo API
            return res.status(response.status).json({
                success: false,
                error: data.error || 'PAYMENT_CREATION_FAILED',
                message: data.message || 'Failed to create payment',
                details: data,
            });
        }
    } catch (error) {
        console.error('PixGo API error:', error);
        return res.status(500).json({
            success: false,
            error: 'INTERNAL_SERVER_ERROR',
            message: 'An error occurred while creating the payment',
        });
    }
}
