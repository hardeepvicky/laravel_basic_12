<?php

namespace App\Exports;

use App\Helpers\Util;
use App\Models\DropDownValue;
use App\Models\ItemCategory;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class SampleItem implements WithMultipleSheets
{
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new class implements FromCollection, WithTitle, WithEvents {
            public function collection()
            {
                // Fetching only active users
                $collection = new Collection([
                    ["Category", "Name", "Color", "Brand", "Size", "Specification", "Description", "Active", "List Display Order"],
                    ["Packing Material | Gatta", "Gatta", "Black", "Addidas", "10x15", "solid", "", "1", "1"],
                ]);

                return $collection;
            }

            public function title(): string
            {
                return 'Item';
            }

            public function registerEvents(): array
            {
                return [
                    AfterSheet::class => function(AfterSheet $event) {
                        $qb = ItemCategory::where("is_active", 1);
                        $item_category_records = ItemCategory::getTreeRecords($qb);
                        $item_category_list = Util::getTreeListArray($item_category_records, "id", "name");

                        $brand_list = DropDownValue::getList("id", "name", ["type" => DropDownValue::BRAND]);
                        $color_list = DropDownValue::getList("id", "name", ["type" => DropDownValue::COLOR]);

                        $sheet = $event->sheet->getDelegate();

                        $validation = $sheet->getCell('A2')->getDataValidation();
                        $validation->setType(DataValidation::TYPE_LIST);
                        $validation->setErrorStyle(DataValidation::STYLE_STOP);
                        $validation->setAllowBlank(false);
                        $validation->setShowInputMessage(true);
                        $validation->setShowErrorMessage(true);
                        $validation->setShowDropDown(true);
                        $validation->setErrorTitle('Invalid Category');
                        $validation->setError('Value is not in list.');
                        $validation->setPromptTitle('Pick from list');
                        $validation->setPrompt('Please select a value from the drop-down list.');
                        $formula = '=Category!$A$1:$A$' . count($item_category_list);
                        $validation->setFormula1($formula);
                        for($r = 2; $r <= 1000; $r++)
                        {
                            $sheet->getCell("A{$r}")->setDataValidation(clone $validation);
                        }

                        $validation = $sheet->getCell('C2')->getDataValidation();
                        $validation->setType(DataValidation::TYPE_LIST);
                        $validation->setErrorStyle(DataValidation::STYLE_STOP);
                        $validation->setAllowBlank(false);
                        $validation->setShowInputMessage(true);
                        $validation->setShowErrorMessage(true);
                        $validation->setShowDropDown(true);
                        $validation->setErrorTitle('Invalid Color');
                        $validation->setError('Value is not in list.');
                        $validation->setPromptTitle('Pick from list');
                        $validation->setPrompt('Please select a value from the drop-down list.');
                        $formula = '=Color!$A$1:$A$' . count($color_list);
                        $validation->setFormula1($formula);
                        for($r = 2; $r <= 1000; $r++)
                        {
                            $sheet->getCell("C{$r}")->setDataValidation(clone $validation);
                        }

                        $validation = $sheet->getCell('D2')->getDataValidation();
                        $validation->setType(DataValidation::TYPE_LIST);
                        $validation->setErrorStyle(DataValidation::STYLE_STOP);
                        $validation->setAllowBlank(false);
                        $validation->setShowInputMessage(true);
                        $validation->setShowErrorMessage(true);
                        $validation->setShowDropDown(true);
                        $validation->setErrorTitle('Invalid Color');
                        $validation->setError('Value is not in list.');
                        $validation->setPromptTitle('Pick from list');
                        $validation->setPrompt('Please select a value from the drop-down list.');
                        $formula = '=Brand!$A$1:$A$' . count($brand_list);
                        $validation->setFormula1($formula);
                        for($r = 2; $r <= 1000; $r++)
                        {
                            $sheet->getCell("D{$r}")->setDataValidation(clone $validation);
                        }
                    },
                ];
            }
        };

        $sheets[] = new class implements FromCollection, WithTitle {
            public function collection()
            {
                $qb = ItemCategory::where("is_active", 1);

                $item_category_records = ItemCategory::getTreeRecords($qb);

                $item_category_list = Util::getTreeListArray($item_category_records, "id", "name");

                $records = [];

                foreach($item_category_list as $category)
                {
                    $records[] = [$category];
                }

                $collection = new Collection($records);

                return $collection;
            }

            public function title(): string
            {
                return 'Category';
            }
        };

        $sheets[] = new class implements FromCollection, WithTitle {
            public function collection()
            {
                $list = DropDownValue::getList("id", "name", ["type" => DropDownValue::BRAND]);

                $records = [];

                foreach($list as $value)
                {
                    $records[] = [$value];
                }

                $collection = new Collection($records);

                return $collection;
            }

            public function title(): string
            {
                return 'Brand';
            }
        };

        $sheets[] = new class implements FromCollection, WithTitle {
            public function collection()
            {
                $list = DropDownValue::getList("id", "name", ["type" => DropDownValue::COLOR]);

                $records = [];

                foreach($list as $value)
                {
                    $records[] = [$value];
                }

                $collection = new Collection($records);

                return $collection;
            }

            public function title(): string
            {
                return 'Color';
            }
        };

        return $sheets;
    }

    
}
