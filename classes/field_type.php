<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace smarturlmeta_identifier;

use mod_smarturl\local\metadata\field_type as field_type_contract;

/**
 * Validates bibliographic ISBN identifiers.
 *
 * @package    smarturlmeta_identifier
 * @copyright  2026 Didactika.org
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class field_type implements field_type_contract {
    /**
     * Identifies the ISBN field type.
     *
     * @return string Stable type key.
     */
    public function key(): string {
        return 'ISBN';
    }

    /**
     * Returns the label shown to editors.
     *
     * @return string Localised label.
     */
    public function label(): string {
        return get_string('isbn', 'smarturlmeta_identifier');
    }
    /**
     * Checks the ISBN checksum and strips spaces and hyphens.
     *
     * @param string $value ISBN.
     * @return string
     */
    public function normalise(string $value): string {
        $isbn = strtoupper(preg_replace('/[\s-]+/', '', trim($value)));
        $sum = 0;
        if (preg_match('/^\d{9}[\dX]$/D', $isbn)) {
            foreach (str_split($isbn) as $index => $digit) {
                $sum += ($digit === 'X' ? 10 : (int)$digit) * (10 - $index);
            }
            if ($sum % 11 === 0) {
                return $isbn;
            }
        } else if (preg_match('/^97[89]\d{10}$/D', $isbn)) {
            foreach (str_split($isbn) as $index => $digit) {
                $sum += (int)$digit * ($index % 2 ? 3 : 1);
            }
            if ($sum % 10 === 0) {
                return $isbn;
            }
        }
        throw new \invalid_parameter_exception(get_string('invalidisbn', 'smarturlmeta_identifier'));
    }
}
