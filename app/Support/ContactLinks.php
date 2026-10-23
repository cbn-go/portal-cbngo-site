<?php

namespace App\Support;

class ContactLinks
{
    /**
     * Normaliza um telefone brasileiro mascarado para dígitos com DDI 55.
     */
    public static function digits(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if ($digits === '') {
            return '';
        }

        return str_starts_with($digits, '55') && strlen($digits) > 11 ? $digits : '55'.$digits;
    }

    /**
     * Gera o link tel: internacional para o telefone informado.
     */
    public static function tel(string $phone): ?string
    {
        $digits = self::digits($phone);

        return $digits !== '' ? 'tel:+'.$digits : null;
    }

    /**
     * Gera o link wa.me para o número de WhatsApp informado.
     */
    public static function whatsapp(string $phone): ?string
    {
        $digits = self::digits($phone);

        return $digits !== '' ? 'https://wa.me/'.$digits : null;
    }

    /**
     * Gera o link de busca do endereço no Google Maps.
     */
    public static function maps(string $address): string
    {
        return 'https://www.google.com/maps/search/?api=1&query='.rawurlencode(trim($address));
    }
}
