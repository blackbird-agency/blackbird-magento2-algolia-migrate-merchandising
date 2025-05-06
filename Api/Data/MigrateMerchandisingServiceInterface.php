<?php
/**
 * MigrateMerchandisingServiceInterface
 *
 * @copyright Copyright © 2025 Blackbird. All rights reserved.
 * @author    emilie (Blackbird Team)
 */

namespace Blackbird\AlgoliaMigrateMerchandising\Api\Data;

interface MigrateMerchandisingServiceInterface
{
    public function migrate(string $storeCode, string $file): void;
}
