// resources/js/utils/MdFormContext.ts
import { reactive, provide, inject, computed, type ComputedRef } from 'vue';
export interface MdFormField {
    validate: () => boolean;
    focus?: () => void;

    isValid?: boolean;
    touched?: boolean;
}

export interface MdFormContext {
    registerField: (key: string, field: MdFormField) => void;
    unregisterField: (key: string) => void;
    validateAll: () => boolean;
    getFields: () => Record<string, MdFormField>;

    isValid: ComputedRef<boolean>;

    isTouched: ComputedRef<boolean>;
}

const MdFormContextKey = Symbol('MdFormContext');

export function useProvideMdForm() {
    const fields = reactive<Record<string, MdFormField>>({});

    const getFields = () => fields;

    const registerField = (key: string, field: MdFormField) => {
        fields[key] = field;
    };

    const unregisterField = (key: string) => {
        delete fields[key];
    };

    const validateAll = (): boolean => {
        let allValid = true;

        Object.entries(fields).forEach(([key, field]) => {
            const ok = field.validate();
            if (!ok) allValid = false;
        });

        return allValid;
    };

    const isValid = computed(() => {
        const list = Object.values(fields);

        if (list.length === 0) return false;

        return list.every((f) => f.isValid === true);
    });

    const isTouched = computed(() => {
        const list = Object.values(fields);
        if (list.length === 0) return false;

        return list.some((f) => f.touched === true);
    });

    const context: MdFormContext = {
        registerField,
        unregisterField,
        validateAll,
        getFields,
        isValid,
        isTouched,
    };

    provide(MdFormContextKey, context);

    return context;
}

export function useMdForm(): MdFormContext | null {
    return inject<MdFormContext | null>(MdFormContextKey, null);
}
