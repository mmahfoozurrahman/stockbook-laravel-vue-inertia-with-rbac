import Swal from 'sweetalert2';

export function useSweetAlert() {
    const toast = (message, icon = 'success') => Swal.fire({
        toast: true,
        position: 'top-end',
        icon,
        title: message,
        showConfirmButton: false,
        timer: 2600,
        timerProgressBar: true,
        customClass: { popup: 'folio-toast' },
    });

    const confirmDelete = (title = 'Remove this record?') => Swal.fire({
        title,
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, remove it',
        cancelButtonText: 'Keep it',
        reverseButtons: true,
        customClass: {
            popup: 'folio-alert',
            confirmButton: 'btn btn-danger px-4',
            cancelButton: 'btn btn-light px-4',
        },
        buttonsStyling: false,
    });

    return { toast, confirmDelete };
}
