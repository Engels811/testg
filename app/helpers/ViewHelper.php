<?php

function e(?string $value, string $fallback = ''): string
{
    return htmlspecialchars($value ?? $fallback, ENT_QUOTES, 'UTF-8');
}
