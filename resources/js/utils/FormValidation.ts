// resources/js/utils/FormValidation.ts
import { reactive } from 'vue';

export interface ValidatableField {
    validate: () => boolean;
}

export interface FieldRefs {
    [key: string]: ValidatableField | null;
}

export function useMdFormValidation() {
    const fieldRefs = reactive<FieldRefs>({});
    let autoId = 0;

    const register = (name: string, el: any) => {
        if (el && typeof el.validate === 'function') {
            fieldRefs[name] = el as ValidatableField;
        } else {
            fieldRefs[name] = null;
        }
    };

    /**
     * Uso 1: :ref="FieldRef('nombre')"
     * Uso 2: :ref="FieldRef"   (se autogenera nombre)
     */
    const FieldRef = (nameOrEl: string | any) => {
        if (typeof nameOrEl === 'string') {
            const name = nameOrEl;
            return (el: any) => register(name, el);
        }

        const el = nameOrEl;
        autoId++;
        const name = `_auto_${autoId}`;
        register(name, el);
    };

    const validateAll = (): boolean => {
        let allValid = true;

        Object.entries(fieldRefs).forEach(([name, field]) => {
            if (field && typeof field.validate === 'function') {
                const ok = field.validate();
                console.log(`Validando "${name}" →`, ok ? 'OK' : 'ERROR');
                if (!ok) allValid = false;
            } else {
                console.log(`"${name}" no tiene metodo validate()`);
            }
        });

        console.log(
            'Resultado final del formulario →',
            allValid ? 'VALIDO' : 'INVALIDO'
        );
        return allValid;
    };

    return {
        fieldRefs,
        FieldRef,
        validateAll,
    };
}
