import type { NextApiRequest, NextApiResponse } from 'next';

// PixGo API Configuration
const PIXGO_API_KEY = 'pk_1234567890abcdef1234567890abcdef1234567890abcdef1234567890abcdef';
const PIXGO_BASE_URL = 'https://pixgo.org/api/v1';

export default async function handler(req: NextApiRequest, res: NextApiResponse) {
    if (req.method !== 'GET') {
        return res.status(405).json({ error: 'Method not allowed' });
    }

    const { paymentId } = req.query;

    if (!paymentId || typeof paymentId !== 'string') {
        return res.status(400).json({ error: 'Payment ID is required' });
    }

    try {
        // Check payment status with PixGo API
        const response = await fetch(`${PIXGO_BASE_URL}/payment/${paymentId}/status`, {
            method: 'GET',
            headers: {
                'X-API-Key': PIXGO_API_KEY,
            },
        });

        const data = await response.json();

        if (response.ok && data.success) {
            return res.status(200).json({
                success: true,
                status: data.data.status,
                paymentId: data.data.payment_id,
                externalId: data.data.external_id,
                amount: data.data.amount,
                customerName: data.data.customer_name,
                customerCpf: data.data.customer_cpf,
                createdAt: data.data.created_at,
                updatedAt: data.data.updated_at,
            });
        } else {
            return res.status(response.status).json({
                success: false,
                error: 'PAYMENT_NOT_FOUND',
                message: 'Payment not found or error retrieving status',
            });
        }
    } catch (error) {
        console.error('PixGo status check error:', error);
        return res.status(500).json({
            success: false,
            error: 'INTERNAL_SERVER_ERROR',
            message: 'An error occurred while checking payment status',
        });
    }
}
