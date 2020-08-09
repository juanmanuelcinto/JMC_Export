<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace JMC\Export\Console\Command\Products;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\File\Csv;

/**
 * Export Categories in a CSV file with the next format:
 * id, url_key
 * Created file location: var (Make sure you have the right permissions).
 * 
 * @author Juan Manuel Cinto <https://github.com/juanmanuelcinto>
 */
class Export extends Command
{

    /**
     * @var Csv
     */
    private $csv;

    /**
     * @var Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $collection;

    /**
     * @var ProductUrlPathGenerator
     */
    private $productUrlPathGenerator;

    /**
     * @var StoreManager
     */
    private $store;
    
   	/**
   	 * @var string
   	 */
   	private $storeId = null;

    /**
     * @param CollectionFactory $collectionFactory
     * @param Csv $csv
     * @param ProductUrlPathGenerator $productUrlPathGenerator
     * @param string $name
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        Csv $csv,
        ProductUrlPathGenerator $productUrlPathGenerator,
        string $name = null
    ) {
        parent::__construct($name);
        $this->collection = $collectionFactory;
        $this->productUrlPathGenerator = $productUrlPathGenerator;
        $this->csv = $csv;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("jmc:export:products");
        $this->setDescription("Export products in CSV file");
        $this->addArgument('store', InputArgument::REQUIRED, __('Type a store ID'));
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Exception
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $storeId = $input->getArgument('store');
        
        if ($storeId) {
        	$this->storeId = $storeId;
        	$output->writeln("Exporting Products... ");
 
        	try {
            	$this->exportCsvFile();
        	} catch (\Exception $e) {
            	$output->writeln($e->getMessage());
        	}
	
        } else {
            $output->writeln("Missing store Id. Please run jmc:export:products <store-id>");
            $output->writeln("To identify the store id you can run the command store:list");
        }
    }
    
    /**
     * Export CSV File
     */
    private function exportCsvFile()
    {
        $exportData = [];

        $products = $this->collection->create()                              
            ->addAttributeToSelect('*')
            ->addStoreFilter($this->storeId);

        if(count($products) > 0){
            foreach ($products as $product) {
                $exportData[] = [
                    $product->getId(),
                    'product',
                    $this->getUrlPathWithSuffix($product)
                ];
            }
            $this->csv->saveData("var/products.csv" , $exportData);
        }
    }
    
    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    private function getUrlPathWithSuffix($product)
    {
        return $this->productUrlPathGenerator->getUrlPathWithSuffix($product, $this->storeId);
    }
}
