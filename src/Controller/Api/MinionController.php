<?php

namespace App\Controller\Api;

use App\Entity\AssignedStates;
use App\Entity\ConnectedMonitors;
use App\Entity\ConnectedPrinters;
use App\Entity\Disk;
use App\Entity\Equipment\Monitor;
use App\Entity\Equipment\MonitorModels;
use App\Entity\Equipment\Printer;
use App\Entity\Equipment\PrinterModel;
use App\Entity\Helpers\CpuModel;
use App\Entity\Helpers\Department;
use App\Entity\Helpers\Manufacturer;
use App\Entity\Helpers\Os;
use App\Entity\Helpers\OsFullName;
use App\Entity\Helpers\ProductName;
use App\Entity\Helpers\Soft;
use App\Entity\Helpers\State;
use App\Entity\Helpers\Type;
use App\Entity\Helpers\TypeDep;
use App\Entity\Helpers\Vendor;
use App\Entity\InstalledSoftware;
use App\Entity\IPs;
use App\Entity\Minion;
use App\Entity\Network;
use App\Repository\ConnectedMonitorsRepository;
use App\Repository\ConnectedPrintersRepository;
use App\Repository\Equipment\MonitorRepository;
use App\Repository\Helpers\DepartmentRepository;
use App\Repository\MinionRepository;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class MinionController
 * @package App\Controller\Api
 * @Route("/api/minion")
 */
class MinionController extends AbstractController
{
    /**
     * @Route("/", name="api_get_all_minion", methods={"GET"})
     */
    public function index(MinionRepository $minionRepository): Response
    {
        $minions = $minionRepository->findAll();

//        $normalizer = new ObjectNormalizer();
//        $serializer = new Serializer([$normalizer]);
//
//        $result = $serializer->normalize($minions, null, [AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true]);
//        return $this->json($minions);
        $data = [];
        foreach ($minions as $minion){
            $networks = $minion->getNetworks();
            $str_network = [];
            $str_mac = [];
            foreach ($networks as $network){
                $ips = $network->getIps();
                foreach ($ips as $ip){
                    array_push($str_network,$ip->getIpAddress());
                }
                array_push($str_mac, $network->getMacAddress()) ;
            }
            $item = [
                'id'  => $minion->getId(),
                'node_name' => $minion->getNodeName(),
                'serialnumber' => $minion->getSerialnumber(),
                'room' => $minion->getRoom(),
                'manufacturer' =>$minion->getManufacturer()->getName(),
                'cpu_model' => $minion->getCpuModel()->getName(),

                'fio_user' => $minion->getFioUser(),
                'user_phone' => $minion->getUserPhone(),
                'ip' => implode(", ",$str_network),
                'mac' => implode(', ', $str_mac),
                'department' => $minion->getDepartment()->getName(),
                'type' => $minion->getType()->getName(),
                'os' => $minion->getOs()->getName(),
                'osrelease' => $minion->getOsrelease(),
                'created_at' => $minion->getCreatedAt(),
                'updated_at' => $minion->getUpdatedAt()
            ];
            array_push($data,$item);
        }

        return $this->json($data);
    }

    /**
     * @Route("/{uuid}", name="update_minion_data", methods={"POST"})
     * @return Response
     */
    public function update_minion_data($uuid, Request $request, LoggerInterface $logger): Response
    {
        $data = json_decode($request->getContent(), true);

        $em = $this->getDoctrine()->getManager();
        $minion = $this->getDoctrine()->getRepository(Minion::class)->find($uuid);
        if (!$minion){
            $minion = new Minion(Uuid::fromString($uuid));
        }

        $minion->setNodeName($data['node_name']);
        $minion->setSerialnumber($data['serialnumber']);
        $minion->setBiosversion($data['biosversion']);
        $minion->setBiosreleasedate(new \DateTime($data['biosreleasedate']));
        $minion->setRoom($data['room']);

        $manufacturer = $this->getDoctrine()->getRepository(Manufacturer::class)->findOneBy(['name' => $data['manufacturer'] ]);
        if(!$manufacturer){
            $manufacturer = new Manufacturer();
            $manufacturer->setName($data['manufacturer']);
            $em->persist($manufacturer);
        }
        $minion->setManufacturer($manufacturer);


        $cpu_model = $this->getDoctrine()->getRepository(CpuModel::class)->findOneBy([
            'name' => $data['cpu_model']
        ]);
        if (!$cpu_model){
            $cpu_model = new CpuModel();
            $cpu_model->setName($data['cpu_model']);
            $em->persist($cpu_model);
        }
        $minion->setCpuModel($cpu_model);

        $minion->setOsrelease($data['osrelease']);

        $os = $this->getDoctrine()->getRepository(Os::class)->findOneBy([
            'name' => $data['os']
        ]);
        if (!$os){
            $os = new Os();
            $os->setName($data['os']);
            $em->persist($os);
        }
        $minion->setOs($os);

        $os_full_name = $this->getDoctrine()->getRepository(OsFullName::class)->findOneBy([
            'name' => $data['osfullname']
        ]);
        if (!$os_full_name){
            $os_full_name = new OsFullName();
            $os_full_name->setName($data['osfullname']);
            $em->persist($os_full_name);
        }
        $minion->setOsFullName($os_full_name);

        $product_name = $this->getDoctrine()->getRepository(ProductName::class)->findOneBy([
            'name' => $data['productname']
        ]);
        if(!$product_name){
            $product_name = new ProductName();
            $product_name->setName($data['productname']);
            $em->persist($product_name);
        }
        $minion->setProductName($product_name);

        $minion->setFioUser($data['fio_user']);
        $minion->setUserPhone($data['user_phone']);

        $type = $this->getDoctrine()->getRepository(Type::class)->findOneBy([
            'name' => $data['type']
        ]);
        if (!$type){
            $type = new Type();
            $type->setName($data['type']);
            $em->persist($type);
        }
        $minion->setType($type);

        $type_dep = $this->getDoctrine()->getRepository(TypeDep::class)->findOneBy([
            'name' => $data['type_dep']
        ]);
        if (!$type_dep){
            $type_dep = new TypeDep();
            $type_dep->setName($data['type_dep']);
            $em->persist($type_dep);
        }
        $minion->setTypeDep($type_dep);

        $department = $this->getDoctrine()->getRepository(Department::class)->findOneBy([
            'name' => $data['department']
        ]);
        if(!$department){
            $department = new Department();
            $department->setName($data['department']);
            $em->persist($department);
        }
        $minion->setDepartment($department);

        $minion->setSaltversion($data['saltversion']);
        $minion->setMemTotal($data['mem_total']);


        $old_networks = $minion->getNetworks();
        foreach ($old_networks as $old_network){
            $intrfs = $old_network->getInterface();
            if (!array_key_exists($intrfs,$data['network'])){
                //$minion->removeNetwork($old_network)
                $em->remove($old_network);
            }
        }

        foreach ($data['network'] as $interface => $network_data){

            $network = $this->getDoctrine()->getRepository(Network::class)->findOneBy([
                'interface' => $interface,
                'minion' => $minion
            ]);
            if (!$network){
                $network = new Network();
                $network->setInterface($interface)->setMacAddress($network_data['mac'])->setMinion($minion);
            }
            $network->setMacAddress($network_data['mac']);


            $old_ips = $network->getIps();

            foreach ($old_ips as $old_ip){
                if (!in_array($old_ip->getIpAddress(),$network_data["ips"])){
                    $em->remove($old_ip);
                }
            }

            foreach ($network_data['ips'] as $ip_value){
                $ip = $this->getDoctrine()->getRepository(IPs::class)->findOneBy([
                    'network' => $network,
                    'ip_address' => $ip_value
                ]);
                if (!$ip){
                    $ip = new IPs();
                    $ip->setNetwork($network)->setIpAddress($ip_value);
                }
                $em->persist($ip);

            }

            $em->persist($network);
        }

        $pkg_info = $data['pkg_info'];

        $old_packages = $minion->getInstalledSoftware();
        foreach ($old_packages as $old_package){
            $pkg_name = $old_package->getSoft()->getName();
            if (!array_search($pkg_name,array_column($pkg_info,'name'))){
                $em->remove($old_package);
            }
        }

        foreach ($pkg_info as $package){

            $soft = $this->getDoctrine()->getRepository(Soft::class)->findOneBy([
                'name' => $package['name']
            ]);
            if (!$soft){
                $soft = new Soft();
                $soft->setName($package['name']);
                $em->persist($soft);
            }

            $installed = $this->getDoctrine()->getRepository(InstalledSoftware::class)->findOneBy([
                'minion' => $minion,
                'soft' => $soft
            ]);
            if (!$installed){
                $installed = new InstalledSoftware();
                $installed->setMinion($minion)->setSoft($soft);
            }
            $installed->setSize($package['size'])->setVersion($package['version']);
            if(array_key_exists('installed_at',$package)){
                $installed->setInstalledAt(\DateTime::createFromFormat('U',$package['installed_at']));
            }
            $em->persist($installed);

        }

        $old_disks = $minion->getDisks();
        $logger->debug('INFO DISK === ',$data['disk_info']);
        foreach ($old_disks as $old_disk){
            $disk_name = $old_disk->getName();
            $logger->debug(
                'DISK_NAME '.$disk_name
            );
            if (!array_key_exists($disk_name,$data['disk_info'])){
                $em->remove($old_disk);
            }
        }

        foreach ($data['disk_info'] as $disk_name => $disk_info){
            $disk = $this->getDoctrine()->getRepository(Disk::class)->findOneBy([
                'minion' => $minion,
                'name' =>$disk_name
            ]);
            if(!$disk){
                $disk = new Disk();
                $disk->setName($disk_name)->setMinion($minion);
            }
            $disk->setAvailable(! is_null($disk_info['available']) ? $disk_info['available'] : "0" )
                ->setCapacity(! is_null($disk_info['capacity']) ? $disk_info['capacity'] : "0")
                ->setBlocks(! is_null($disk_info['1K-blocks']) ?  $disk_info['1K-blocks'] : "0" )
                ->setFilesystem( $disk_info['filesystem'])
                ->setUsed(! is_null($disk_info['used']) ? $disk_info['used'] : "0");

            $em->persist($disk);
        }


        $states = array_key_exists('states',$data) ? $data['states'] : [];
        $old_states = $minion->getAssignedStates();
        foreach ($old_states as $old_state){
            $state_name = $old_state->getState();
            if(!in_array($state_name->getName(),$states)){
                $em->remove($old_state);
            }
        }

        foreach ($states as $state_info){

            $state = $this->getDoctrine()->getRepository(State::class)->findOneBy([
                'name' => $state_info
            ]);
            if (!$state){
                $state = new State();
                $state->setName($state_info);
                $em->persist($state);
            }
            $assigned_states = $this->getDoctrine()->getRepository(AssignedStates::class)->findOneBy([
                'minion' => $minion,
                'state' => $state
            ]);
            if (!$assigned_states){
                $assigned_states = new AssignedStates();
                $assigned_states->setMinion($minion)->setState($state);
                $em->persist($assigned_states);
            }


        }

        $monitors = array_key_exists('monitors',$data) ? $data['monitors'] : [];
        $logger->debug("MONITOR === ",$monitors);
        $old_monitors = $minion->getConnectedMonitors();
        foreach ($old_monitors as $old_monitor){
            $mnt = $old_monitor->getMonitor();
            $serial_num = $mnt->getSerial();
            $logger->debug($serial_num);
            $model_name = $mnt->getModel()->getName();
            $logger->debug($model_name);
            $connected = false;
            foreach ($monitors as $monitor){
                if ( ($monitor['serial']===$serial_num) && ($monitor['model']===$model_name) ) {
                    $connected = true;
                    break;
                }
            }
            if (!$connected){
                $old_monitor->setConnected(false);
                $em->persist($old_monitor);
            }

        }
        foreach ($monitors as $monitor){
            $serial = $monitor['serial'];
            $year = $monitor['year'];
            $week = $monitor['week'];
            $model_str = $monitor['model'];
            $vendor_str = $monitor['vendor'];

            $vendor = $this->getDoctrine()->getRepository(Vendor::class)->findOneBy([
                'name' => $vendor_str
            ]);
            if(!$vendor){
                $vendor = new Vendor();
                $vendor->setName($vendor_str);
                $em->persist($vendor);
            }

            $model = $this->getDoctrine()->getRepository(MonitorModels::class)->findOneBy([
                'name' => $model_str,
                'vendor' => $vendor
            ]);
            if(!$model){
                $model = new MonitorModels();
                $model->setName($model_str)->setVendor($vendor);
                $em->persist($model);
            }

            $c_monitor = $this->getDoctrine()->getRepository(Monitor::class)->findOneBy([
                'serial' => $serial,
                'model' => $model
            ]);
            if(!$c_monitor){
                $c_monitor = new Monitor();
                $c_monitor->setSerial($serial)->setModel($model)->setYear($year)->setYear($year)->setWeek($week);
                $em->persist($c_monitor);
            }

            $connected_monitor = $this->getDoctrine()->getRepository(ConnectedMonitors::class)->findOneBy([
                'minion' => $minion,
                'monitor' => $c_monitor,
                'connected' => true
            ]);
            if (!$connected_monitor){
                $connected_monitor = new ConnectedMonitors();
                $connected_monitor->setMinion($minion)->setMonitor($c_monitor)
                                  ->setConnected(true)->setCdate(new \DateTime());
                $em->persist($connected_monitor);
            }


        }

        ///printers
        $printers = array_key_exists('printers',$data) ? $data['printers'] : [];
        $old_printers = $minion->getConnectedPrinters();
        foreach ($old_printers as $old_printer){
            $mnt = $old_printer->getPrinter();
            $serial_num = $mnt->getSerial();
            $model_name = $mnt->getModel()->getName();
            $connected = false;
            foreach ($printers as $printer){
                if ( ($printer['serial']===$serial_num) && ($printer['model']===$model_name) ) {
                    $connected = true;
                    break;
                }
            }
            if (!$connected){
                $old_printer->setConnected(false);
                $em->persist($old_printer);
            }

        }
        foreach ($printers as $printer){
            $serial = $printer['serial'];
            $model_str = $printer['model'];
            $vendor_str = $printer['vendor'];

            $vendor = $this->getDoctrine()->getRepository(Vendor::class)->findOneBy([
                'name' => $vendor_str
            ]);
            if(!$vendor){
                $vendor = new Vendor();
                $vendor->setName($vendor_str);
                $em->persist($vendor);
            }

            $model = $this->getDoctrine()->getRepository(PrinterModel::class)->findOneBy([
                'name' => $model_str,
                'vendor' => $vendor
            ]);
            if(!$model){
                $model = new PrinterModel();
                $model->setName($model_str)->setVendor($vendor);
                $em->persist($model);
            }

            $c_printer = $this->getDoctrine()->getRepository(Printer::class)->findOneBy([
                'serial' => $serial,
                'model' => $model
            ]);
            if(!$c_printer){
                $c_printer = new Printer();
                $c_printer->setSerial($serial)->setModel($model);
                $em->persist($c_printer);
            }

            $connected_printers = $this->getDoctrine()->getRepository(ConnectedPrinters::class)->findOneBy([
                'minion' => $minion,
                'printer' => $c_printer,
                'connected' => true
            ]);
            if (!$connected_printers){
                $connected_printers = new ConnectedPrinters();
                $connected_printers->setMinion($minion)->setPrinter($c_printer)
                    ->setConnected(true)->setCdate(new \DateTime());
                $em->persist($connected_printers);
            }


        }
        /// end printers


        $em->persist($minion);

        $em->flush();

        return $this->json(
            [
                'res' => $uuid,
                'data' => $data
            ]
        );
    }

    /**
     * @Route("/info", name="minion_info", methods={"GET"})
     * @return Response
     */
    public function info(): Response{
        $d = $this->getDoctrine()->getRepository(Minion::class)->count_info();

        return $this->json($d);
    }

    /**
     * @Route("/new_minions", name="new_minions_list", methods={"GET"})
     * @param MinionRepository $minionRepository
     * @return JsonResponse
     */
    function new_minions(MinionRepository $minionRepository):JsonResponse
    {
        $result = $minionRepository->new_minions();
        return $this->json($result);
    }

    /**
     * @Route("/{uuid}", name="minion_info_detail", methods={"GET"})
     * @param string $uuid
     * @param MinionRepository $minionRepository
     * @return JsonResponse
     */
    public function minion_info(string $uuid, MinionRepository $minionRepository,ConnectedMonitorsRepository $connectedMonitorsRepository, ConnectedPrintersRepository $connectedPrintersRepository):JsonResponse
    {
        $minion = $minionRepository->find($uuid);

        $networks = $minion->getNetworks();

        $str_network = [];
        foreach ($networks as $network){
            $ips = $network->getIps();

            $str_ip = [];
            foreach ($ips as $ip){
                array_push($str_ip,$ip->getIpAddress());
            }

            array_push($str_network, [
                'macaddr' => $network->getMacAddress(),
                'ips'  => implode(', ',$str_ip)
            ]) ;
        }

        $disks = $minion->getDisks();

        $disks_info =[];
        foreach ($disks as $disk){
            $disks_info[] = [
                'name' => $disk->getName(),
                'used' => $disk->getUsed(),
                'available' => $disk->getAvailable(),
                'blocks' => $disk->getBlocks(),
                'filesystem' => $disk->getFilesystem(),
                'capacity' => $disk->getCapacity(),
            ];
        }

        $disk_names = array_column($disks_info,'name');
        $disks_ordered = array_multisort($disk_names,SORT_ASC,$disks_info);

        $softs = $minion->getInstalledSoftware();
        $soft_list = [];
        foreach ($softs as $soft){
            $soft_list[] = [
                'id' => $soft->getId(),
                'name' =>$soft->getSoft()->getName(),
                'soft_id' =>$soft->getSoft()->getId(),
                'size' =>$soft->getSize(),
                'version' => $soft->getVersion()
            ];
        }

        $states = $minion->getAssignedStates();
        $state_list = [];
        foreach ($states as $state){
            $state_list[] = $state->getState()->getName();
        }

       // $monitors = $minion->getConnectedMonitors();
        $monitors = $connectedMonitorsRepository->findActual($minion);
        $monitor_list = [];
        foreach ($monitors as $monitor){
            $monitor_list[] = [
                'serial' => $monitor->getMonitor()->getSerial(),
                'model' => $monitor->getMonitor()->getModel()->getName()
            ];
        }

        $printers = $connectedPrintersRepository->findActual($minion);
        $printer_list = [];
        foreach ($printers as $printer){
            $printer_list[] = [
                'serial' => $printer->getPrinter()->getSerial(),
                'model' => $printer->getPrinter()->getModel()->getName()
            ];
        }

        $data = [
            'node_name' => $minion->getNodeName(),
            'serialnumber' => $minion->getSerialnumber(),
            'biosversion' => $minion->getBiosversion(),
            'biosreleasedate' => $minion->getBiosreleasedate(),
            'manufacturer' => $minion->getManufacturer()->getName(),
            'cpu_model'  => $minion->getCpuModel()->getName(),
            'product_name' => $minion->getProductName()->getName(),
            'room' => $minion->getRoom(),
            'fio_user' => $minion->getFioUser(),
            'user_phone' => $minion->getUserPhone(),
            'type' => $minion->getType()->getName(),
            'type_dep' => $minion->getTypeDep()->getName(),
            'department' => $minion->getDepartment()->getName(),
            'saltversion' => $minion->getSaltversion(),
            'os' => $minion->getOs()->getName(),
            'osrelease' => $minion->getOsrelease(),
            'os_full_name' => $minion->getOsFullName()->getName(),
            'network' => $str_network,
            'disks' => $disks_info,
            'disks_ordered' => $disks_ordered,
            'soft' => $soft_list,
            'states' => $state_list,
            'monitors' => $monitor_list,
            'printers' => $printer_list,
            'created_at' => $minion->getCreatedAt(),
            'updated_at' => $minion->getUpdatedAt()

        ];

        return $this->json($data);
    }

}
