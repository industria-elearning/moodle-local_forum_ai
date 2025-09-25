<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Servicio para integración con APIs de AI.
 *
 * Clase encargada de generar respuestas usando modelos de AI y
 * validar la configuración de conexión.
 *
 * @package    local_forum_ai
 * @copyright  2025 Piero Llanos <piero@datacurso.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_forum_ai;

/**
 * Servicio para integración con APIs de AI
 */
class ai_service {

    /**
     * Genera respuesta usando API de AI (placeholder para implementación futura)
     *
     * @param string $originalmessage Mensaje original
     * @param string $context Contexto adicional
     * @param string $model Modelo de AI a usar
     * @return string Respuesta generada
     */
    public static function generate_response($originalmessage, $context = '', $model = 'gpt-3.5') {

        // Por ahora retorna respuesta mockeada.
        return "Respuesta generada por AI (modelo: {$model}) - Esta es una implementación placeholder.";
    }
    /**
     * Valida configuración de API.
     *
     * @param string $model Modelo de AI a validar
     * @return bool
     */
    public static function validate_api_config($model) {
        return true;
    }
}
