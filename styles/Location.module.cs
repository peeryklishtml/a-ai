.overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }

    to {
        opacity: 1;
    }
}

.modal {
    background: white;
    border-radius: 12px;
    padding: 40px 30px;
    max-width: 500px;
    width: 90%;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    animation: slideUp 0.3s ease;
    text-align: center;
}

@keyframes slideUp {
    from {
        transform: translateY(50px);
        opacity: 0;
    }

    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.title {
    font-size: 1.8rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 10px;
}

.subtitle {
    font-size: 1rem;
    color: #666;
    margin-bottom: 30px;
}

.highlightRed {
    color: #dc3545;
    font-weight: bold;
}

.formGroup {
    margin-bottom: 20px;
    text-align: left;
}

.label {
    display: block;
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 8px;
    font-weight: 500;
}

.select {
    width: 100%;
    padding: 12px 15px;
    font-size: 1rem;
    border: 2px solid #ddd;
    border-radius: 8px;
    background: white;
    cursor: pointer;
    transition: all 0.2s;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23333' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 15px center;
    padding-right: 40px;
}

.select:hover {
    border-color: #6f2c91;
}

.select:focus {
    outline: none;
    border-color: #6f2c91;
    box-shadow: 0 0 0 3px rgba(111, 44, 145, 0.1);
}

.select:disabled {
    background-color: #f5f5f5;
    cursor: not-allowed;
    opacity: 0.6;
}

.btnPrimary {
    width: 100%;
    padding: 14px 20px;
    font-size: 1.1rem;
    font-weight: bold;
    color: white;
    background: #28a745;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
    margin-top: 10px;
}

.btnPrimary:hover:not(:disabled) {
    background: #218838;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
}

.btnPrimary:disabled {
    background: #ccc;
    cursor: not-allowed;
    transform: none;
}

.loadingContainer {
    padding: 60px 20px;
}

.loadingTitle {
    font-size: 1.5rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 15px;
}

.loadingText {
    font-size: 1rem;
    color: #666;
    margin-bottom: 30px;
}

.spinner {
    width: 50px;
    height: 50px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #6f2c91;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}

.successContainer {
    padding: 40px 20px;
}

.checkIcon {
    width: 80px;
    height: 80px;
    border: 4px solid #28a745;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 25px;
    animation: scaleIn 0.5s ease;
}

@keyframes scaleIn {
    0% {
        transform: scale(0);
        opacity: 0;
    }

    50% {
        transform: scale(1.1);
    }

    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.checkIcon::before {
    content: '✓';
    font-size: 3rem;
    color: #28a745;
    font-weight: bold;
}

.successText {
    font-size: 1.1rem;
    color: #333;
    line-height: 1.6;
    margin-bottom: 25px;
}

.distance {
    font-weight: bold;
    color: #6f2c91;
}

.btnSuccess {
    width: 100%;
    padding: 14px 20px;
    font-size: 1.1rem;
    font-weight: bold;
    color: white;
    background: #28a745;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
}

.btnSuccess:hover {
    background: #218838;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
}
