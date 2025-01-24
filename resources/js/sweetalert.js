import Swal from 'sweetalert2';

export const showSuccess = (message) => {
    Swal.fire({
        title: 'Succès',
        text: message,
        icon: 'success',
        confirmButtonText: 'OK'
    });
};

export const showError = (message) => {
    Swal.fire({
        title: 'Erreur',
        text: message,
        icon: 'error',
        confirmButtonText: 'OK'
    });
};

export const showInfo = (message) => {
    Swal.fire({
        title: 'Info',
        text: message,
        icon: 'info',
        confirmButtonText: 'OK'
    });
};
