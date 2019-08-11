<?php
/**
 * Created by PhpStorm.
 * Date: 19-2-4
 * Time: 下午3:44
 */

namespace Tests\Unit;


use App\ComputeInstance;
use App\Node\ComputeNode;
use App\Utils\ComputeInstance\ConfigurationBuilder;
use App\Utils\Node\ComputeNode\ComputeNodeUtil;
use App\Utils\Node\ComputeNode\Exception\Exception;
use Tests\TestCase;
use YunInternet\CCMSCommon\Network\Exception\APIRequestException;

class ComputeNodeUtilTest extends TestCase
{
    private $nodeId;

    public function __construct(string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->nodeId = @$_SERVER["NODE_ID"];
    }

    public function testCreateUtil()
    {
        $this->assertTrue($this->createUtil() instanceof ComputeNodeUtil);
    }

    public function testPing()
    {
        $pingResult = $this->createUtil()->ping($rawResponse);
        var_dump($rawResponse);
        $this->assertTrue($pingResult);
    }

    public function testNodeInformation()
    {
        $nodeInformation = $this->createUtil()->nodeInfo();
        var_dump($nodeInformation);
        $this->assertArrayHasKey("model", $nodeInformation);
    }

    public function testSetupDomain()
    {
        /**
         * @var ComputeInstance $computeInstance
         */
        $computeInstance = ComputeInstance::query()->where("unique_id", "ci-1pnzmic3keeb")->firstOrFail();

        /**
         * @var ComputeNodeUtil $util
         */
        $util = $computeInstance->node()->firstOrFail()->createUtil();

        try {
            print_r($util->instanceSetupRequest(ConfigurationBuilder::buildConfiguration($computeInstance)));
        } catch (APIRequestException $e) {
            print $e->getRawResponse();
            throw $e;
        }

        $this->assertTrue(true);
    }

    public function testReconfigureDomain()
    {
        $computeInstance = ComputeInstance::query()->firstOrFail();

        /**
         * @var ComputeNodeUtil $util
         */
        $util = $computeInstance->node()->firstOrFail()->createUtil();

        try {
            print_r($util->instanceReconfigureRequest($computeInstance->unique_id, ConfigurationBuilder::buildConfiguration($computeInstance)));
        } catch (APIRequestException $e) {
            print $e->getRawResponse();
            throw $e;
        }

        $this->assertTrue(true);
    }

    public function testDeleteDomain()
    {
        /**
         * @var ComputeInstance $computeInstance
         */
        $computeInstance = ComputeInstance::query()->where("unique_id", "ci-1pnjdlx3aqnn")->firstOrFail();

        /**
         * @var ComputeNodeUtil $util
         */
        $util = $computeInstance->node()->firstOrFail()->createUtil();

        try {
            print_r($util->deleteInstance($computeInstance->unique_id, true));
            // $computeInstance->delete();
        } catch (APIRequestException $e) {
            print $e->getRawResponse();
            throw $e;
        }

        $this->assertTrue(true);
    }

    /**
     * @return ComputeNodeUtil
     */
    public function createUtil()
    {
        static $util = null;
        if (is_null($util))
            $util = ComputeNode::query()->findOrFail($this->nodeId)->createUtil();
        return $util;
    }
}