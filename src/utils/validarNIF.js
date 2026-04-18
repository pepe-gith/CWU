const LETRAS = 'TRWAGMYFPDXBNJZSQVHLCKE'

export function validarNIF(valor) {
    const nif = valor.trim().toUpperCase()

    if (/^\d{8}[A-Z]$/.test(nif)) {
        return LETRAS[parseInt(nif.slice(0, 8)) % 23] === nif[8]
    }

    // NIE: X → 0, Y → 1, Z → 2
    if (/^[XYZ]\d{7}[A-Z]$/.test(nif)) {
        const num = nif.replace('X', '0').replace('Y', '1').replace('Z', '2')
        return LETRAS[parseInt(num.slice(0, 8)) % 23] === nif[8]
    }

    return false
}
