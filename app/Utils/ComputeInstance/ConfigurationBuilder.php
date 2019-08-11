<?php
/**
 * Created by PhpStorm.
 * Date: 19-2-19
 * Time: 下午2:51
 */

namespace App\Utils\ComputeInstance;


use App\ComputeInstance;
use App\IPPool\IPv4Assignment;
use App\IPPool\IPv6Assignment;

/**
 * Class ConfigurationBuilder
 * Build json format configuration for slave API
 * @package App\Utils\ComputeInstance
 */
class ConfigurationBuilder
{
    private $computeInstance;

    public function __construct(ComputeInstance $computeInstance)
    {
        $this->computeInstance = $computeInstance;
    }

    public static function buildConfiguration(ComputeInstance $computeInstance)
    {
        $configurationBuilder = new ConfigurationBuilder($computeInstance);
        return $configurationBuilder->build();
    }

    public function build()
    {
        $configuration = $this->computeInstance->instanceSize();
        $configuration["unique_id"] = $this->computeInstance->unique_id;
        $configuration["uuid"] = $this->computeInstance->uuid;
        $configuration["hostname"] = $this->computeInstance->hostname;
        $configuration["machine_type"] = $this->computeInstance->machine_type;
        $configuration["password"] = $this->computeInstance->password;
        $configuration["vnc_password"] = $this->computeInstance->vnc_password;
        $configuration["machine_type"] = $this->computeInstance->machine_type;
        $configuration["use_legacy_bios"] = $this->computeInstance->use_legacy_bios;
        $configuration["no_clean_traffic"] = $this->computeInstance->no_clean_traffic;
        $configuration["volumes"] = $this->volumes();
        $configuration["networkInterfaces"] = $this->networkInterfaces();
        $configuration["cdroms"] = $this->cdroms();
        $configuration["floppies"] = $this->floppies();

        return $configuration;
    }

    public function volumes()
    {
        $volumes = [];

        foreach ($this->computeInstance->attachedLocalVolumes()->orderBy("attach_order")->get() as $volume) {
            $volumeConfiguration = [
                "unique_id" => $volume->unique_id,
                "capacity" => $volume->capacity * 1024,
                "bus" => $volume->bus,
            ];

            if ($image = $volume->image) {
                $volumeConfiguration["backing_store"] = [
                    "type" => 0,
                    "image" => $image->internal_name,
                    "force_version" => $image->force_version,
                ];
            }

            $volumes[] = $volumeConfiguration;
        }

        return $volumes;
    }

    /**
     * @param null|string $macOrId Only return configuration for specific network interface
     * @return array
     */
    public function networkInterfaces($macOrId = null)
    {
        $networkInterfaces = [];
        /**
         * @var ComputeInstance\Device\NetworkInterface $networkInterface
         */
        foreach ($this->computeInstance->networkInterfaces as $networkInterface) {
            if (!is_null($macOrId)) {
                if (is_numeric($macOrId) && $networkInterface->id !== $macOrId)
                    continue;
                if (is_string($macOrId) && $networkInterface->mac_address !== $macOrId)
                    continue;
            }
            $ipv4Addresses = $networkInterface->ipv4Addresses()->with("pool")->get();
            $ipv6Addresses = $networkInterface->ipv6Addresses()->with("pool")->get();

            $ipv4AddressList = [];
            /**
             * @var IPv4Assignment $ipv4Address
             */
            foreach ($ipv4Addresses as $ipv4Address) {
                $ipv4AddressList[] = [
                    "ip" => $ipv4Address->human_readable_first_usable,
                    "mask" => $ipv4Address->pool->subnet_network_bits,
                    "pool_mask" => $ipv4Address->pool->network_bits,
                    "pool_gateway" => $ipv4Address->pool->human_readable_gateway,
                ];
            }

            $ipv6AddressList = [];
            /**
             * @var IPv6Assignment $ipv6Address
             */
            foreach ($ipv6Addresses as $ipv6Address) {
                $ipv6AddressList[] = [
                    "ip" => $ipv6Address->human_readable_first_usable,
                    "mask" => $ipv6Address->pool->subnet_network_bits,
                    "pool_mask" => $ipv6Address->pool->network_bits,
                    "pool_gateway" => $ipv6Address->pool->human_readable_gateway,
                ];
            }


            $values = [
                "type" => $networkInterface->type,
                "model" => $networkInterface->model,
                "ipv4Addresses" => $ipv4AddressList,
                "ipv6Addresses" => $ipv6AddressList,
            ];
            if (!empty($networkInterface->mac_address))
                $values["mac_address"] = $networkInterface->mac_address;

            $networkInterfaces[] = $values;
        }

        return $networkInterfaces;
    }

    public function cdroms()
    {
        $cdroms = [];
        /**
         * @var ComputeInstance\Device\CDROM $cdrom
         */
        foreach ($this->computeInstance->cdroms as $cdrom) {
            $internalName = null;
            if ($cdrom->media)
                $internalName = $cdrom->media->internal_name;
            $cdroms[] = [
                "type" => $cdrom->media_type,
                "internalName" => $internalName
            ];
        }

        return $cdroms;
    }

    public function floppies()
    {
        $floppies = [];
        /**
         * @var ComputeInstance\Device\Floppy $floppy
         */
        foreach ($this->computeInstance->floppies as $floppy) {
            $internalName = null;
            if ($floppy->media)
                $internalName = $floppy->media->internal_name;
            $floppies[] = [
                "type" => $floppy->media_type,
                "internalName" => $internalName
            ];
        }

        return $floppies;
    }
}