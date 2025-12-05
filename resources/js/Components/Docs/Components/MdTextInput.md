# MdTextInput

Componente `MdTextInput` — parte del sistema **MaterialDesign (Md*)**.

## Props principales

(Descripción resumida, basada en comportamiento real.)

- `modelValue`: Valor enlazado.
- `label`: Etiqueta visible.
- `required`: Habilita validación obligatoria.
- `readonly`: Solo lectura.
- `pattern`: Expresión regular opcional.
- `externalError`: Mensaje de error externo.
- `showSuccessState`: Activa borde verde al validar.
- `density`, `variant`, `rounded`: Controles visuales.
- Otras props específicas del componente.

## Métodos expuestos
```js
ref.validate()   // Ejecuta validación interna
ref.focus?.()    // Solo en algunos componentes
```

## Ejemplo básico
```vue
<FormValidate @submit="handle">
  <MdTextInput
    ref="field"
    v-model="form.campo"
    label="Ejemplo de MdTextInput"
    :required="true"
  />
  <v-btn type="submit">Enviar</v-btn>
</FormValidate>
```

## Validación
- Valida automáticamente `required`, formatos, longitudes o reglas especiales según el componente.
- Participa en `FormValidate`, que ejecuta `.validate()` de cada campo registrado.

## Notas específicas
- Cada componente tiene validaciones internas adaptadas:
  - **TextInput**: allowed types, uppercase, patterns.
  - **EmailInput**: regex de correo predefinida.
  - **PhoneInput**: validación numérica flexible.
  - **NumberInput**: sanitización, límites y negativos.
  - **PasswordInput**: niveles de seguridad.
  - **DateInput / DatePicker**: fechas simples o rangos.
  - **Select / SelectSearch**: min/max seleccionados.
  - **Switch / Checkbox / Toggle**: booleanos.
  - **Textarea / RichText**: longitudes, HTML limpio.
  - **FileInput / UploadArea**: size, formato, múltiple.
  - **TimeInput**: formato 24h o AM/PM.
  - **Slider**: rangos y pasos.
