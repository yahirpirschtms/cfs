<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class CFSboardRepository
{
    public function getProjectsWithMastersAndSubprojects(): Collection{
        // Query: Proyectos + Masters
        $projectsMasters = DB::table('cfs_project as p')
            ->leftJoin('cfs_generic_catalogs as g_user', 'p.drayage_user', '=', 'g_user.gnct_id')
            ->leftJoin('cfs_generic_catalogs as g_file', 'p.drayage_typefile', '=', 'g_file.gnct_id')
            ->leftJoin('cfs_generic_catalogs as g_invoice', 'p.invoice', '=', 'g_invoice.gnct_id')
            ->leftJoin('cfs_master as m', function($join) {
                $join->on('p.project_id', '=', 'm.fk_project_id')
                     ->where('m.status', '1');
            })
            ->where('p.status', '1')
            ->select(
                'p.project_id',
                DB::raw('DATE_FORMAT(p.month, "%b/%e") as month'),
                DB::raw('DATE_FORMAT(p.month, "%m/%d/%Y") as month_full'),
                'p.invoice',
                'p.drayage_user',
                'g_user.gntc_description as drayage_user_desc',
                'p.drayage_typefile',
                'g_file.gntc_description as drayage_file_desc',
                'g_invoice.gntc_description as invoice_desc',
                'm.mbl',
                'm.container_number',
                'm.total_pieces',
                'm.total_pallets',
                'm.fk_project_id',
                'm.eta_port',
                'm.arrival_date',
                'm.lfd',
                'm.notes'
            )
            ->get()
            ->groupBy('project_id');

        // Query: Subprojects de esos masters
        $masterIds = $projectsMasters->flatten()->pluck('mbl')->filter()->unique();

        $subprojects = DB::table('cfs_subprojects as s')
            ->leftJoin('cfs_generic_catalogs as cfs', 's.cfs_comment', '=', 'cfs.gnct_id')
            ->leftJoin('cfs_generic_catalogs as cr', 's.customs_release_comment', '=', 'cr.gnct_id')
            ->whereIn('s.fk_mbl', $masterIds)
            ->where('s.status', '1')
            ->select(
                's.fk_mbl',
                's.hbl',
                's.subprojects_id',
                'cfs.gntc_value as cfs_value',
                's.cfs_comment',
                'cr.gntc_value as cr_value',
                's.customs_release_comment'
            )
            ->get()
            ->groupBy('fk_mbl');

        // Armamos jerarquía proyecto → masters → subprojects
        $projects = [];
        foreach ($projectsMasters as $projId => $masters) {
            $first = $masters->first();
            $projects[$projId] = (object) [
                'project_id' => $projId,
                'month' => $first->month,
                'month_full' => $first->month_full,
                'invoice' => $first->invoice,
                'drayage_user' => $first->drayage_user,
                'drayage_user_desc' => $first->drayage_user_desc,
                'drayage_typefile' => $first->drayage_typefile,
                'drayage_file_desc' => $first->drayage_file_desc,
                'invoice_desc' => $first->invoice_desc,
                'masters' => []
            ];

            foreach ($masters as $master) {
                if (!$master->mbl) continue;

                $masterSubs = $subprojects->get($master->mbl, collect());
                foreach ($masterSubs as $sub) {
                    $sub->cfscomment_relation = $sub->cfs_value ?? $sub->cfs_desc;
                    $sub->customrelease_relation = $sub->cr_value ?? $sub->cr_desc;
                }

                $projects[$projId]->masters[] = (object) [
                    'mbl' => $master->mbl,
                    'fk_project_id' => $master->fk_project_id,
                    'container_number' => $master->container_number,
                    'total_pieces' => $master->total_pieces,
                    'total_pallets' => $master->total_pallets,
                    'eta_port' => $master->eta_port,
                    'notes' => $master->notes,
                    'arrival_date' => $master->arrival_date,
                    'lfd' => $master->lfd,
                    'subprojects' => $masterSubs
                ];
            }
        }

        return collect(array_values($projects));
    }

    public function getProjectsWithSubprojects(?string $projectId = null, ?string $masterId = null): array{
        // Proyectos + Masters
        $projectsMasters = DB::table('cfs_project as p')
            ->leftJoin('cfs_generic_catalogs as g_user', 'p.drayage_user', '=', 'g_user.gnct_id')
            ->leftJoin('cfs_generic_catalogs as g_file', 'p.drayage_typefile', '=', 'g_file.gnct_id')
            ->leftJoin('cfs_generic_catalogs as g_invoice', 'p.invoice', '=', 'g_invoice.gnct_id')
            ->leftJoin('cfs_master as m', function($join) {
                $join->on('p.project_id', '=', 'm.fk_project_id')
                     ->where('m.status', '1');
            })
            ->where('p.status', '1')
            ->select(
                'p.project_id',
                DB::raw('DATE_FORMAT(p.month, "%b/%e") as month'),
                DB::raw('DATE_FORMAT(p.month, "%m/%d/%Y") as month_full'),
                'p.invoice',
                'p.drayage_user',
                'g_user.gntc_description as drayage_user_desc',
                'p.drayage_typefile',
                'g_file.gntc_description as drayage_file_desc',
                'g_invoice.gntc_description as invoice_desc',
                'm.mbl',
                'm.container_number',
                'm.total_pieces',
                'm.total_pallets',
                'm.fk_project_id',
                'm.eta_port',
                'm.arrival_date',
                'm.lfd',
                'm.notes'
            )
            ->get()
            ->groupBy('project_id');

        $masterIds = $projectsMasters->flatten()->pluck('mbl')->filter()->unique();

        // Subprojects + Partnumbers + HBL References
        $subprojectsRaw = DB::table('cfs_subprojects as s')
            ->leftJoin('cfs_generic_catalogs as cfs', 's.cfs_comment', '=', 'cfs.gnct_id')
            ->leftJoin('cfs_generic_catalogs as cr', 's.customs_release_comment', '=', 'cr.gnct_id')
            ->leftJoin('cfs_customer as cus', 's.customer', '=', 'cus.pk_customer')
            ->leftJoin('cfs_h_pn as hp', 's.hbl', '=', 'hp.fk_hbl')
            ->leftJoin('cfs_pn as pn', 'hp.fk_pn', '=', 'pn.pk_part_number')
            ->leftJoin('cfs_hbl_references as hbl', 's.hbl', '=', 'hbl.fk_hbl')
            ->whereIn('s.fk_mbl', $masterIds)
            ->where('s.status', '1')
            ->select(
                's.*',
                'cus.pk_customer',
                'cus.description as customer_desc',
                'pn.pk_part_number',
                'pn.description as pn_desc',
                'hbl.pk_hbl_reference',
                'hbl.description as hbl_desc',
                'cfs.gntc_value as cfs_value',
                'cfs.gntc_description as cfs_desc',
                'cr.gntc_value as cr_value',
                'cr.gntc_description as cr_desc'
            )
            ->get();

        // Agrupar Subprojects con Partnumbers y HBL References
        $subprojectsGrouped = $subprojectsRaw->groupBy('fk_mbl')->map(function($group) {
            return $group->groupBy('hbl')->map(function($subgroup) {
                $sub = $subgroup->first();

                $partnumbers = $subgroup->map(function($item) {
                    return [
                        'pk_part_number' => $item->pk_part_number,
                        'description' => $item->pn_desc
                    ];
                })->unique('pk_part_number')->values();

                $hblreferences = $subgroup->pluck('hbl_desc', 'pk_hbl_reference')->map(function($desc, $pk) {
                    return ['pk_hbl_reference' => $pk, 'description' => $desc];
                })->values();

                return (object) array_merge((array) $sub, [
                    'partnumbers' => $partnumbers,
                    'hblreferences' => $hblreferences,
                ]);
            });
        });

        // Construir jerarquía Proyecto → Masters → Subprojects
        $projects = [];
        $selectedSubprojects = collect();

        foreach ($projectsMasters as $projId => $masters) {
            $first = $masters->first();
            $projects[$projId] = (object) [
                'project_id' => $projId,
                'month' => $first->month,
                'month_full' => $first->month_full,
                'invoice' => $first->invoice,
                'drayage_user' => $first->drayage_user,
                'drayage_user_desc' => $first->drayage_user_desc,
                'drayage_typefile' => $first->drayage_typefile,
                'drayage_file_desc' => $first->drayage_file_desc,
                'invoice_desc' => $first->invoice_desc,
                'masters' => []
            ];

            foreach ($masters as $master) {
                if (!$master->mbl) continue;

                $masterSubs = $subprojectsGrouped->get($master->mbl, collect());

                if ($projId == $projectId && $master->mbl == $masterId) {
                    $selectedSubprojects = $masterSubs;
                }

                $projects[$projId]->masters[] = (object) [
                    'mbl' => $master->mbl,
                    'fk_project_id' => $master->fk_project_id,
                    'container_number' => $master->container_number,
                    'total_pieces' => $master->total_pieces,
                    'total_pallets' => $master->total_pallets,
                    'eta_port' => $master->eta_port,
                    'arrival_date' => $master->arrival_date,
                    'lfd' => $master->lfd,
                    'notes' => $master->notes,
                    'subprojects' => $masterSubs->values()
                ];
            }
        }

        return [
            'projects' => array_values($projects),
            'selectedSubprojects' => $selectedSubprojects->values()
        ];
    }
}
