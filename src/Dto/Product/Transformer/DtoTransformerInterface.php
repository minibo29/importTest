<?php

namespace App\Dto\Product\Transformer;

interface DtoTransformerInterface
{
    public function transformToDto($entity);
}