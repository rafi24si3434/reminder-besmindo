// Global Besmindo Reminder Scripts

document.addEventListener('DOMContentLoaded', () => {
    // Auto initialize tooltips or interactive behaviors if needed
});

// Helper for sending direct WhatsApp
function openWhatsApp(phone, message) {
    let cleanPhone = phone.replace(/[^0-9]/g, '');
    if (cleanPhone.startsWith('0')) {
        cleanPhone = '62' + cleanPhone.substring(1);
    }
    const encodedMsg = encodeURIComponent(message);
    const url = `https://wa.me/${cleanPhone}?text=${encodedMsg}`;
    window.open(url, '_blank');
}

// Confirmation helper
function confirmAction(title, text, confirmBtnText = 'Ya, Lanjutkan') {
    return Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#0284c7',
        cancelButtonColor: '#64748b',
        confirmButtonText: confirmBtnText,
        cancelButtonText: 'Batal',
        background: '#1e293b',
        color: '#f8fafc'
    });
}
