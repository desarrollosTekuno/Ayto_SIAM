import Swal from "sweetalert2";

const customSwal = (options) => {
    return Swal.fire({
        confirmButtonColor: "#43A047",
        cancelButtonColor: "#BDBDBD",
        confirmButtonText: "Confirmar",
        cancelButtonText: "Cancelar",
        denyButtonColor: "#D32F2F",
        denyButtonText: "Denegar",
        ...options,
    });
};

const customToastSwal = (options) => {
    return Swal.fire({
        showCancelButton: false,
        showConfirmButton: false,
        showDenyButton: false,
        timer: 2500,
        timerProgressBar: true,
        toast: true,
        position: "top-end",
        customClass: {
            container: "my-swal",
        },
        ...options,
    });
};


const customConfirmSwal = (options) => {
    return Swal.fire({
        text: "Esta acción es irreversible",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#6a1f3c",
        cancelButtonColor: "#41504d",
        // confirmButtonText: "Si, eliminar",
        confirmButtonText: "Si, Confirmar",
        cancelButtonText: "No, cancelar",
        denyButtonText: "Denegar",
        reverseButtons: true,
        showLoaderOnConfirm: true,
        allowOutsideClick: false,
        allowEscapeKey: false,
        customClass: {
            container: "my-swal",
        },
        ...options,
    });
};

const successToast = (title = "Guardado correctamente") => {
    return Swal.fire({
        icon: "success",
        title,
        toast: true,
        timer: 2000,
        position: "top-end",
        showConfirmButton: false,
        customClass: {
            container: "my-swal",
        },
    });
};

const infoToast = (title = "Revise por favor") => {
    return Swal.fire({
        icon: "info",
        title,
        toast: true,
        timer: 2000,
        position: "top-end",
        showConfirmButton: false,
        customClass: {
            container: "my-swal",
        },
    });
};

const warningToast = (title = "Revise por favor") => {
    return Swal.fire({
        icon: "warning",
        title,
        toast: true,
        timer: 2000,
        position: "top-end",
        showConfirmButton: false,
        customClass: {
            container: "my-swal",
        },
    });
};

const errorToast = (title = "Error al procesar la acción") => {
    return Swal.fire({
        icon: "error",
        title,
        toast: true,
        timer: 2500,
        position: "top-end",
        showConfirmButton: false,
        customClass: {
            container: "my-swal",
        },
    });
};

export {
    customConfirmSwal,
    customSwal,
    customToastSwal,
    successToast,
    infoToast,
    warningToast,
    errorToast
};
