# Componentes – Arquitectura Actualizada SIAM

## 1. Estructura Global del Proyecto

### Frontend
- resources/js/app.js → bootstrap global (Vuetify, Toast, Notiflix, AgGrid, sockets)
- resources/js/global.js → componentes legacy (en retiro)
- resources/js/loading.js → loader global Inertia
- resources/js/utils/MdFormContext.ts → validación centralizada
- resources/js/Components/FormValidate.vue → wrapper de formularios
- resources/js/Components/AgGrid.vue → grid unificado
- resources/js/Components/MaterialDesign/* → inputs estándar nuevos
- resources/js/Components/Vuetify/* → botones y diálogos

### Backend
- app/Models/Traits/HasAgGrid.php → contrato server-side
- Controllers → método grid()
- Routes → POST /recurso/grid

---

## 2. Sistema de Validación (CORE)

### MdFormContext
Ubicación:
```
resources/js/utils/MdFormContext.ts
```

Responsabilidades:
- Registro dinámico de campos
- validateAll()
- isValid / isTouched
- focus automático

Campos deben exponer:
- validate(): boolean
- focus(): void (opcional)

---

### FormValidate
Ubicación:
```
resources/js/Components/FormValidate.vue
```

Uso:
```vue
<FormValidate ref="formRef" @submit="guardar" @invalid="onInvalid">
```

Flujo:
1. submit()
2. validateAll()
3. ❌ invalid → focus primer campo
4. ✅ submit → Inertia

Slots:
- validateAll
- isSubmitting
- isValid
- isTouched

---

## 3. Props Comunes (MaterialDesign)

### Props compartidas (casi todos los Md*)
| Prop | Tipo | Descripción |
|---|---|---|
| modelValue | any | v-model |
| label | string | etiqueta visible |
| tooltip | string | tooltip (si aplica, aparece en label) |
| required | boolean | obligatorio |
| externalError | string | error backend |
| showSuccessState | boolean | borde/estado verde |
| id / name | string | key para MdFormContext |
| readonly | boolean | solo lectura |

### Helper / Hint (cambio nuevo)
Se estandarizó el “texto de ayuda” para que sea consistente y no “invada” el layout:

- Componentes basados en **v-text-field** (MdTextInput, MdEmailInput, MdNumberInput, MdPasswordInput, MdDateInput, MdDatePicker, MdSelect, MdSelectSearch, MdTimeInput, MdFileInput):
  - Prop: `helper?: string`
  - Se muestra usando `hint`.
  - Por defecto **se muestra sólo al focus** (Vuetify).  
  - Si el componente trae `persistent-hint` activado internamente, el helper se verá siempre (esto depende del componente).

- Componentes que **renderizan helper manualmente** (o que no soportan hint nativo):
  - Se agregó soporte de “helper no invasivo”:
    - `helperPersistent?: boolean` (default `false`)
    - Si `false` → el helper se muestra solo tras interacción (`touched/dirty`)
    - Si `true` → el helper se muestra siempre (mientras no haya error)

Componentes ya actualizados con este esquema:
- MdSlider (`helper`, `helperPersistent`)
- MdSwitch (`helper`, `helperPersistent`)
- MdToggle (`helper`, `helperPersistent`)
- MdUploadArea (`description` como helper, `helperPersistent`)

---

## 4. Componentes MaterialDesign

### MdTextInput
Notas:
- Default visual: `density="compact"` y `rounded="lg"` (cambio nuevo).
- Soporta `minLength`, `maxLength`, `pattern`, `counter`, `allowed` (sanitizado por tipo).
- Helper vía `helper`.

### MdEmailInput / MdNumberInput / MdPasswordInput
Notas:
- Misma base de validación local + `externalError`.
- Helper vía `helper`.
- (Password) soporta `security` (simple/basic/strong/strict).

### MdSelect / MdSelectSearch
Notas:
- Soportan single/multiple, límites (`minSelected`, `maxSelected`).
- Helper vía `helper`.

### MdDateInput / MdDatePicker / MdTimeInput
Notas:
- Helper vía `helper`.
- DateInput: `mode` soporta `date` y `datetime-local`.

### MdFileInput / MdUploadArea
Notas:
- FileInput: `maxSizeMB`, `accept`, `multiple`, `chips`, `counter`.
- UploadArea: `description` (helper) + `helperPersistent` (nuevo) + `maxFiles`.

### MdCheckbox / MdRadioGroup / MdSwitch / MdToggle / MdSlider
Notas:
- Switch/Toggle/Slider ya integran helper no invasivo (`helperPersistent`).
- Checkbox/RadioGroup mantienen label con tooltip y error bajo control.

Lista completa:
- MdTextInput
- MdTextArea
- MdSelect
- MdSelectSearch
- MdCheckbox
- MdSwitch
- MdToggle
- MdRadioGroup
- MdSlider
- MdTimeInput
- MdFileInput
- MdUploadArea
- MdRichTextArea

Todos:
- Se registran en MdFormContext
- Validación local + backend
- focus automático

---

## 5. AgGrid Unificado

Ubicación:
```
resources/js/Components/AgGrid.vue
```

Modos:
- Client-side
- Server-side (infinite)

Props:
- serverUrl
- initialColumnDefs
- idVista
- extraParams
- requestHeaders

Métodos:
- reloadServerSide()
- resetTable()
- ExportToCsv()

---

## 6. Contrato Server-side

Payload:
```json
{ "startRow":0,"endRow":50,"sortModel":[],"filterModel":{} }
```

Respuesta:
```json
{ "rows":[], "lastRow":150, "total":150 }
```

---

## 7. Trait HasAgGrid

Ubicación:
```
app/Models/Traits/HasAgGrid.php
```

Soporta:
- filtros
- orden
- joins
- paginación
- mapeo columnas

---

## 8. Migración Legacy → Nuevo Sistema

Antes:
- Inputs legacy
- Validación manual

Ahora:
- Md*
- FormValidate
- MdFormContext

Checklist:
- Reemplazar inputs
- Envolver con FormValidate
- Usar ref.submit()
- Aprovechar `helper` + `counter` + `minLength/maxLength/pattern` para acercar UI a reglas de BD

---

## 9. Estado Global

- app.js → plugins globales
- loading.js → isLoading reactivo

---

Documento oficial de referencia técnica y migración.
