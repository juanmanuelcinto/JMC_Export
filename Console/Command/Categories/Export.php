<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace JMC\ExportCategories\Console\Command\Categories;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
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

    const NAME_ARGUMENT = "name";
    const NAME_OPTION = "option";

    /**
     * @var Csv
     */
    private $csv;

    /**
     * @var Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    private $collection;
    
    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var CategoryUrlPathGenerator
     */
    private $categoryUrlPathGenerator;

    /**
     * @var StoreManager
     */
    private $store;

    /**
     * @param CollectionFactory $collectionFactory
     * @param Csv $csv
     * @param CategoryRepositoryInterface $categoryRepository
     * @param CategoryUrlPathGenerator $categoryUrlPathGenerator
     * @param StoreManagerInterface $store
     * @param string $name
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        Csv $csv,
        CategoryRepositoryInterface $categoryRepository,
        CategoryUrlPathGenerator $categoryUrlPathGenerator,
        StoreManagerInterface $store,
        string $name = null
    ) {
        parent::__construct($name);
        $this->collection = $collectionFactory;
        $this->categoryRepository = $categoryRepository;
        $this->categoryUrlPathGenerator = $categoryUrlPathGenerator;
        $this->csv = $csv;
        $this->store = $store;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("jmc:export:categories");
        $this->setDescription("Export categories in CSV file");
        $this->setDefinition([
            new InputArgument(self::NAME_ARGUMENT, InputArgument::OPTIONAL, "Name"),
            new InputOption(self::NAME_OPTION, "-a", InputOption::VALUE_NONE, "Option functionality")
        ]);
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
        $name = $input->getArgument(self::NAME_ARGUMENT);
        $option = $input->getOption(self::NAME_OPTION);
        $output->writeln("Exporting Categories... ");

        try {
            $this->exportCsvFile();
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }
    
    /**
     * Export CSV File
     */
    private function exportCsvFile()
    {
        $exportData = [];

        $categories = $this->collection->create()                              
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('is_active','1');

        if(count($categories) > 0){
            foreach ($categories as $category) {
                $exportData[] = [
                    $category->getId(),
                    $this->getUrlPathWithSuffix($category)
                ];
            }
            $this->csv->saveData("var/categories.csv" , $exportData);
        }
    }
    
    /**
     * @param \Magento\Catalog\Model\Category $category
     * @return string
     */
    private function getUrlPathWithSuffix($category)
    {
        return $this->categoryUrlPathGenerator->getUrlPathWithSuffix($category);
    }
}
