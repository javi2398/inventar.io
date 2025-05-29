import { showModificableAlert } from "./alerts"

const validarFormularioInsercionMuestra = (form) => {

    if (form.codigoMuestra.length < 1){
        showModificableAlert('Rellene todos los campos', 'El código de muestra se encuentra vacío', 'error')
        return false

    } else if (form.fecha.length < 1){
        showModificableAlert('Rellene todos los campos', 'La fecha se encuentra vacía', 'error')
        return false

    } else if (form.tipoNaturaleza.length < 1){
        showModificableAlert('Rellene todos los campos', 'El tipo de naturaleza se encuentra vacía', 'error')
        return false

    } else if ((form.tipoNaturaleza == 1 || form.tipoNaturaleza == 2) && form.organo < 1 ){
        showModificableAlert('Rellene todos los campos', 'El organo biopsiado se encuentra vacío', 'error')
        return false

    }

    return true
}