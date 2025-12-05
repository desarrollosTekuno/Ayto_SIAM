# SIAM Md* Components

Conjunto de componentes de formularios basados en **Vuetify 3 + Vue 3** con:

- Estilo uniforme (Material Design)
- Validación integrada por campo (`validate()`)
- Orquestación de formularios mediante `FormValidate`
- Soporte pensado para Inertia + Laravel

Este paquete no es una librería NPM independiente: está pensado como **design system interno** dentro del mismo proyecto (ej. SIAM / Bazvic).

---

## Requisitos

- Vue 3
- Vuetify 3
- Inertia.js (opcional pero recomendado)
- TypeScript opcional (los componentes están preparados para TS, pero se pueden usar desde vistas JS sin problema)

---

## Estructura de archivos

```txt
resources/
  js/
    Components/
      FormValidate.vue
      MaterialDesign/
        MdTextInput.vue
        MdEmailInput.vue
        MdPhoneInput.vue
        MdNumberInput.vue
        MdPasswordInput.vue
        MdTextareaInput.vue
        MdRichTextArea.vue
        MdDateInput.vue
        MdDatePicker.vue
        MdTimeInput.vue
        MdSelect.vue
        MdSelectSearch.vue
        MdRadioGroup.vue
        MdCheckbox.vue
        MdSwitch.vue
        MdToggle.vue
        MdSlider.vue
        MdFileInput.vue
        MdUploadArea.vue
        index.ts          // barrel file (exports de todos los Md*)
        md-generated.ts   // archivo autogenerado (no editar a mano)
    utils/
      MdFormContext.ts   // contexto de formulario interno
