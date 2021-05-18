<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\ImportProductAction;

/**
 * @ApiResource(
 *     collectionOperations={
 *      "post"={
 *          "method"="POST",
 *          "controller"=ImportProductAction::class,
 *          "deserialize"=false,
 *          "openapi_context"={
 *                 "requestBody"={
 *                     "content"={
 *                         "multipart/form-data"={
 *                             "schema"={
 *                                 "type"="object",
 *                                 "properties"={
 *                                     "file"={
 *                                         "type"="string",
 *                                         "format"="binary"
 *                                     }
 *                                 }
 *                             }
 *                         }
 *                     }
 *                 }
 *             }
 *          }
 *     }
 * )
 */


class ImportProduct
{
    /**
     *@ApiProperty(identifier=true)
     */
    public int $total;

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @param int $total
     */
    public function setTotal(int $total): void
    {
        $this->total = $total;
    }
}
