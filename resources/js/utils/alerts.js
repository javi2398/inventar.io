import Swal from "sweetalert2";

export const showErrorAlert = () => {
    Swal.fire({
        title: "Error al enviar el formulario",
        text: "Complete todos los campos",
        icon: "error",
        confirmButtonText: "Volver",
    });
};

export const showSuccessAlert = () => {
    Swal.fire({
        position: "top-end",
        icon: "success",
        title: "Su análisis ha sido guardado",
        showConfirmButton: false,
        timer: 2000,
    });
};


export const showModificableAlert = (title, text, icon = "success") => {
    Swal.fire({
        title,
        text,
        icon,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
    });
};
