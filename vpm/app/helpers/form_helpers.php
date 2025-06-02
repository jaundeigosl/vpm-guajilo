<?php
function formInput(string $name, string $label, string $value = '', bool $required = true, string $type = 'text', string $placeholder = ''): string {
    $requiredAttr = $required ? 'required' : '';
    $placeholderAttr = $placeholder ?: $label;
    $escapedValue = htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    return "
        <div class='form-row'>
            <label for='{$name}'>{$label}</label>
            <input type='{$type}' name='{$name}' id='{$name}' placeholder='{$placeholderAttr}' value='{$escapedValue}' {$requiredAttr}>
        </div>
    ";
}

function formCheckbox(string $name, string $label, $checked = false): string {
    $isChecked = $checked ? 'checked' : '';
    return "<label><input type='checkbox' name='{$name}' value='1' {$isChecked}> {$label}</label>";
}

function formSelect(string $name, string $label, array $options, $selected = null, bool $required = false): string {
    $html = "<label for='$name'>$label</label>";
    $html .= "<select name='$name' id='$name'" . ($required ? " required" : "") . ">";
    $html .= "<option value=''>- Seleccionar -</option>";
    foreach ($options as $option) {
        $value = $option->id ?? $option['id'] ?? null;
        $text = $option->name ?? $option['name'] ?? '';
        $isSelected = $value == $selected ? 'selected' : '';
        $html .= "<option value='$value' $isSelected>" . htmlspecialchars($text) . "</option>";
    }
    $html .= "</select>";
    return $html;
}