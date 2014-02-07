<?php

namespace Trucker\Transporters;

class TransporterFactory
{
    /**
     * Create a transporter instance based on configuration
     * 
     * @param  string $transport
     * @return \Trucker\Transporters\TransportableInterface
     */
    public static function build($transport)
    {
        switch ($transport)
        {
            case 'json':
                return new JsonTransporter;
        }

        throw new \InvalidArgumentException("Unsupported transporter [{$transport}]");
    }
}
