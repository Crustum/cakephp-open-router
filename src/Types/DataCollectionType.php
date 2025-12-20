<?php
declare(strict_types=1);

namespace Crustum\OpenRouter\Types;

/**
 * This class keeps data collection setting types.
 */
final readonly class DataCollectionType
{
    /**
     * allow: (default) allow providers which store user data non-transiently and may train on it.
     */
    public const string ALLOW = 'allow';

    /**
     * deny: use only providers which do not collect user data.
     */
    public const string DENY = 'deny';
}
