<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Capaian_kinerja extends MY_Controller {
    public $data = array();
    
    function __construct()
    {
        parent::__construct();
        // Your own constructor code
        $this->load->library("Aauth");
        
        if (!$this->aauth->is_loggedin()) {
        	$this->session->set_flashdata('message_type', 'error');
                $this->session->set_flashdata('messages', 'Please login first.');
                redirect('login');
        }
        
        $this->load->model('Capaian_kinerja_model');
        $this->load->model('Utama_model');
        $this->data['modul'] = $this->aauth->get_module_id($this->uri->segment(1));
        $this->data['users']                = $this->aauth->get_user();
        $this->data['groups']               = $this->aauth->get_user_groups();
        $this->data['pegawai'] = $this->Utama_model->get_pegawai_by_nip($this->data['users']->nip);
        $bagian = !empty($this->data['pegawai']) ? $this->data['pegawai']->bagian : "";
        $this->data['bagian'] = $this->Utama_model->get_bagian_by_id($bagian);
        $groups = "";
        foreach ($this->data['groups'] as $key => $val){
            $groups .= $val->group_id.","; 
        }
        $this->data['group_arr'] = substr_replace($groups, "", -1);
        $this->data['menu_list'] = $this->Utama_model->get_list_menu($this->data['group_arr'], $this->data['modul']);
    }
    
    function index()
    {
        $is_permit = $this->aauth->control_no_redirect('capaian_kinerja_ekinerja_perm');
        if(!$is_permit) {
            $this->session->set_flashdata('notification', $this->lang->line('aauth_error_no_access'));
            redirect('no_permission');
        }
        
        $this->data['bc_parent'] = "E-Kinerja";
        $this->data['bc_child'] = "Capaian Kinerja";
        $this->data['serapan'] = $this->Capaian_kinerja_model->get_serapan_bulan(date('M Y'));
        $perms = "capaian_kinerja_ekinerja_perm";
        $comments = "Halaman Capaian Kinerja";
        $this->aauth->logit($perms, current_url(), $comments);
    	$this->load->view('capaian_kinerja', $this->data);
    }
    
    public function ajax_get_serapan(){
        $bulan = $this->input->get('bulan',TRUE);
        
        $serapan = $this->Capaian_kinerja_model->get_serapan_bulan($bulan);
        
        if(count((array)$serapan) > 0) {
            $res = array(
                'success' => true,
                'messages' => "Data found",
                'nilai' => $serapan->nilai." %"
                );
        } else {
            $res = array(
                'success' => false,
                'messages' => "Serapan tidak ditemukan",
                'nilai' => "-"
                );
        }
        echo json_encode($res);
    }
    
    public function ajax_aktifitas_list(){
        $filter_bln = $this->input->get('filter_bln',TRUE);
        $list = $this->Capaian_kinerja_model->get_aktifitas_list($this->data['pegawai']->id_pegawai, $filter_bln);
        $data = array();
        $no = isset($_GET['start']) ? $_GET['start'] : 0;
        
        foreach($list as $aktifitas){
            $no++;
            $target_per_bulan = $aktifitas->qty / 12;
            $row = array();
            $row[] = $no;
            $row[] = $aktifitas->skp;
            $row[] = $aktifitas->waktu_skp;
            $row[] = number_format($target_per_bulan,1);
            $row[] = $aktifitas->volume;
            $row[] = $aktifitas->capaian;
            
            $data[] = $row;
        }
        $output = array(
                    "draw" => $this->input->get('draw'),
                    "recordsTotal" => $this->Capaian_kinerja_model->count_aktifitas_all($this->data['pegawai']->id_pegawai, $filter_bln),
                    "recordsFiltered" => $this->Capaian_kinerja_model->count_aktifitas_filtered($this->data['pegawai']->id_pegawai, $filter_bln),
                    "data" => $data,
                    );
        //output to json format
        echo json_encode($output);
    }
    
    public function ajax_get_jml_waktu_efektif(){
        $bulan = $this->input->get('bulan',TRUE);
        $id_pegawai = $this->data['pegawai']->id_pegawai;
        $tempat_tugas = $this->data['pegawai']->tempat_tugas;
        
        $waktu_kurang = $this->Capaian_kinerja_model->get_jumlah_tidak_masuk($bulan, $id_pegawai);
        $jml_hari_kerja = $this->Capaian_kinerja_model->get_jumlah_hari_kerja($bulan);
        $hari_kerja = isset($jml_hari_kerja->jml_hari) ? $jml_hari_kerja->jml_hari : 0;
        $menit_per_hari = isset($jml_hari_kerja->menit_per_hari) ? $jml_hari_kerja->menit_per_hari : 0;
        $get_poin_capaian = $this->Capaian_kinerja_model->get_jumlah_capaian($id_pegawai, $bulan);
        $get_perilaku = $this->Capaian_kinerja_model->get_perilaku($id_pegawai, $bulan);
        $persen_perilaku = isset($get_perilaku->persen_prilaku) ? $get_perilaku->persen_prilaku : 0;
        $serapan = $this->Capaian_kinerja_model->get_serapan_bulan($bulan);
        $persen_serapan = isset($serapan->nilai) ? $serapan->nilai : 0;
        $nilai_capaian = isset($get_poin_capaian->capaian) ? $get_poin_capaian->capaian : 0;
        
        //jumlah menit kerja dalam sebulan
        if(count((array)$waktu_kurang) > 0) {
            if($tempat_tugas == 8 or $tempat_tugas == 9){
                $menit_kerja = JML_MENIT_SHIFT;
            }else{
                $menit_kerja = intval($hari_kerja) * $menit_per_hari;
            }

            //ABSENT
            $menit_alfa = $menit_per_hari*2;
            $menit_tanpa_alasan = isset($waktu_kurang->tanpa_alasan) ? intval($waktu_kurang->tanpa_alasan)*$menit_alfa : 0;
            $jml_tanpa_alasan = isset($waktu_kurang->tanpa_alasan) ? $waktu_kurang->tanpa_alasan : 0;
           
            //TERLAMBAT
            $menit_terlambat = isset($waktu_kurang->terlambat_menit) ? $waktu_kurang->terlambat_menit : 0;
            
            //PULANG CEPAT
            $menit_plg_cepat = isset($waktu_kurang->pulang_cepat_menit) ? $waktu_kurang->pulang_cepat_menit : 0;
            
            //IZIN 
            // $menit_izin = $waktu_kurang->izin * $menit_per_hari;
            $menit_izin = $waktu_kurang->izin * $menit_per_hari;
            $jml_izin = isset($waktu_kurang->izin) ? $waktu_kurang->izin : 0;
            
            //SAKIT
            $menit_sakit = isset($waktu_kurang->sakit) ? intval($waktu_kurang->sakit)*$menit_per_hari : 0;
            $jml_sakit = isset($waktu_kurang->sakit) ? $waktu_kurang->sakit : 0;
            
            //IZIN SETENGAH HARI -150
            $ish_pengurangan = $menit_per_hari/2;
            $menit_izin_setengah_hari = isset($waktu_kurang->izin_setengah_hari) ? intval($waktu_kurang->izin_setengah_hari)* $ish_pengurangan : 0;
            $jml_izin_setengah_hari = isset($waktu_kurang->izin_setengah_hari) ? $waktu_kurang->izin_setengah_hari : 0;
            
            //SAKIT SURAT DOKTER -240
            $ssd_pengurangan = $menit_per_hari - 60;
            $menit_sakit_srt_dokter = isset($waktu_kurang->sakit_srt_dokter) ? intval($waktu_kurang->sakit_srt_dokter)* $ssd_pengurangan : 0;
            $jml_sakit_srt_dokter = isset($waktu_kurang->sakit_srt_dokter) ? $waktu_kurang->sakit_srt_dokter : 0;
            
            //CUTI SAKIT   -60
            $cs_pengurangan = $menit_per_hari-60;
            $menit_cuti_sakit = isset($waktu_kurang->cuti_sakit) ? intval($waktu_kurang->cuti_sakit)* $cs_pengurangan : 0;
            $jml_cuti_sakit = isset($waktu_kurang->cuti_sakit) ? $waktu_kurang->cuti_sakit : 0;
            
            // CUTI SAKIT RAWAT INAP RS   -150
            $csrirs = $menit_per_hari-150;
            $menit_cuti_sakit_ranap_rs = isset($waktu_kurang->cuti_sakit_ranap_rs) ? intval($waktu_kurang->cuti_sakit_ranap_rs)* $csrirs : 0;
            $jml_cuti_sakit_ranap_rs = isset($waktu_kurang->cuti_sakit_ranap_rs) ? $waktu_kurang->cuti_sakit_ranap_rs : 0;

            //TOTAL PENGURANGAN
            $total_pengurangan = $menit_tanpa_alasan + $menit_terlambat + $menit_plg_cepat + $menit_izin + $menit_sakit + $menit_izin_setengah_hari + $menit_sakit_srt_dokter + $menit_cuti_sakit + $menit_cuti_sakit_ranap_rs ;
            $hasil_pengurangan = $menit_kerja - $total_pengurangan;


            //CUTI ALASAN PENTING
            $menit_cuti_alasan_penting = isset($waktu_kurang->cuti_alasan_penting) ? intval($waktu_kurang->cuti_alasan_penting)*$menit_per_hari : 0;
            $jml_cuti_alasan_penting = isset($waktu_kurang->cuti_alasan_penting) ? $waktu_kurang->cuti_alasan_penting : 0;
            
            
            //ISOMAN   +300
            $menit_isoman = isset($waktu_kurang->covid) ? intval($waktu_kurang->covid)*$menit_per_hari : 0;
            $jml_isoman = isset($waktu_kurang->covid) ? $waktu_kurang->covid : 0;
            
            //RANAP C19    +300
            $menit_ranapc19 = isset($waktu_kurang->ranapc19) ? intval($waktu_kurang->ranapc19)*$menit_per_hari : 0;
            $jml_ranapc19 = isset($waktu_kurang->covid) ? $waktu_kurang->covid : 0;

            // CUTI TAHUNAN +300
            $menit_cuti_tahunan = isset($waktu_kurang->cuti_tahunan) ? intval($waktu_kurang->cuti_tahunan)* $menit_per_hari : 0;
            $jml_cuti_tahunan = isset($waktu_kurang->cuti_tahunan) ? $waktu_kurang->cuti_tahunan : 0;


            // CUTI BERSALIN    +300
            //jika cuti bersalin > 10 masuk pengurangan
            $cuti_bersalin = isset($waktu_kurang->cuti_bersalin) ? intval($waktu_kurang->cuti_bersalin) : 0;
            $menit_cuti_bersalin = isset($waktu_kurang->cuti_bersalin) ? intval($waktu_kurang->cuti_bersalin)* $menit_per_hari : 0;
            $jml_cuti_bersalin = isset($waktu_kurang->cuti_bersalin) ? $waktu_kurang->cuti_bersalin : 0;

            // CUTI BESAR   +300
            $menit_cuti_besar = isset($waktu_kurang->cuti_besar) ? intval($waktu_kurang->cuti_besar)* $menit_per_hari : 0;
            $jml_cuti_besar = isset($waktu_kurang->cuti_besar) ? $waktu_kurang->cuti_besar : 0;

            // DINAS LUAR AKHIR +300
            $menit_dinas_luar_akhir = isset($waktu_kurang->dinas_luar_akhir) ? intval($waktu_kurang->dinas_luar_akhir)* $menit_per_hari : 0;
            $jml_dinas_luar_akhir = isset($waktu_kurang->dinas_luar_akhir) ? $waktu_kurang->dinas_luar_akhir : 0;

            // DINAS LUAR AWAL  +300
            $menit_dinas_luar_awal = isset($waktu_kurang->dinas_luar_awal) ? intval($waktu_kurang->dinas_luar_awal)* $menit_per_hari : 0;
            $jml_dinas_luar_awal = isset($waktu_kurang->dinas_luar_awal) ? $waktu_kurang->dinas_luar_awal : 0;

            // JARI TDK TERBACA +300
            $menit_tidak_terbaca = isset($waktu_kurang->tidak_terbaca) ? intval($waktu_kurang->tidak_terbaca)* $menit_per_hari : 0;
            $jml_tidak_terbaca = isset($waktu_kurang->tidak_terbaca) ? $waktu_kurang->tidak_terbaca : 0;

            // DINAS LUAR PENUH +300
            $menit_dinas_luar_penuh = isset($waktu_kurang->dinas_luar_penuh) ? intval($waktu_kurang->dinas_luar_penuh)* $menit_per_hari : 0;
            $jml_dinas_luar_penuh = isset($waktu_kurang->dinas_luar_penuh) ? $waktu_kurang->dinas_luar_penuh : 0;


            // CUTI BERSALIN ANAK KE3   +300
            $menit_cuti_bersalin_ak3 = isset($waktu_kurang->cuti_bersalin_ak3) ? intval($waktu_kurang->cuti_bersalin_ak3)* $menit_per_hari : 0;
            $jml_cuti_bersalin_ak3 = isset($waktu_kurang->cuti_bersalin_ak3) ? $waktu_kurang->cuti_bersalin_ak3 : 0;


            

            

            //TOTAL PENAMBAHAN
            $total_penambahan = $menit_cuti_alasan_penting + $menit_isoman + $menit_ranapc19 + $menit_cuti_tahunan + $menit_cuti_bersalin + $menit_cuti_besar + $menit_dinas_luar_akhir + $menit_dinas_luar_awal + $menit_tidak_terbaca + $menit_dinas_luar_penuh + $menit_cuti_bersalin_ak3;
            $hasil_penambahan = $nilai_capaian + $total_penambahan;
            $poin_capaian = min($hasil_penambahan,$hasil_pengurangan);
            
            if ($poin_capaian == 0){
                $persen_capaian = 0;
                $capaian70 = 0;
            }else{
                //pembaginya menggunakan menit kerja aktif si pegawai
                $persen_capaian = ($poin_capaian/$menit_kerja)*100;
                $capaian70 = $persen_capaian*0.7;
                //$persen_capaian = $persen_capaian-$persen_kurang;
            }

            $capaian70_fix = ($capaian70 >= 70) ? 70 : number_format($capaian70,2);
            $total_nilai = $capaian70_fix+$persen_perilaku+$persen_serapan;
                //jika cuti > 10 persentasi kinerja dibagi 2
                if($cuti_bersalin > 10){
                    //total kinerja dibagi 2
                    $total_nilai = $total_nilai / 2;
                }else {
                    $total_nilai;
                }

            


            $tabel_waktu = "<tr>
                                <td> 1 </td>
                                <td> Absen </td>
                                <td> <span id='label_izin'>".$jml_tanpa_alasan."</span> </td>
                                <td> ".$menit_alfa."</td>
                                <td> ".$menit_tanpa_alasan." </td>
                            </tr>
                            <tr>
                                <td> 2 </td>
                                <td> Terlambat </td>
                                <td> - </td>
                                <td> - </td>
                                <td> ".$menit_terlambat."</td>
                            </tr>
                            <tr>
                                <td> 3 </td>
                                <td> Pulang Cepat </td>
                                <td> - </td>
                                <td> - </td>
                                <td> ".$menit_plg_cepat." </td>
                            </tr>
                            <tr>
                                <td> 4 </td>
                                <td> Izin </td>
                                <td> <span id='label_izin'>".$jml_izin."</span> </td>
                                <td> ".$menit_per_hari."</td>
                                <td> ".$menit_izin." </td>
                            </tr>
                            <tr>
                                <td> 5 </td>
                                <td> Sakit </td>
                                <td> <span id='label_sakit'>".$jml_sakit."</span> </td>
                                <td> ".$menit_per_hari."</td>
                                <td> ".$menit_sakit." </td>
                            </tr>
                            <tr>
                                <td> 6 </td>
                                <td> Izin Setengah Hari </td>
                                <td> <span id='label_izin_setangah_hari'>".$jml_izin_setengah_hari."</span> </td>
                                <td> ".$ish_pengurangan."</td>
                                <td> ".$menit_izin_setengah_hari." </td>
                            </tr>
                            <tr>
                                <td> 7 </td>
                                <td> Sakit dg Surat Dokter </td>
                                <td> <span id='label_sakit_srt_dokter'>".$jml_sakit_srt_dokter."</span> </td>
                                <td> ".$ssd_pengurangan."</td>
                                <td> ".$menit_sakit_srt_dokter." </td>
                            </tr>
                            <tr>
                                <td> 8 </td>
                                <td> Cuti Sakit </td>
                                <td> <span id='label_cuti_sakit'>".$jml_cuti_sakit."</span> </td>
                                <td> ".$cs_pengurangan."</td>
                                <td> ".$menit_cuti_sakit." </td>
                            </tr>
                            <tr>
                                <td> 9 </td>
                                <td> Cuti Sakit Rawat Inap RS </td>
                                <td> <span id='label_cuti_sakit_ranap_rs'>".$jml_cuti_sakit_ranap_rs."</span> </td>
                                <td> ".$csrirs."</td>
                                <td> ".$menit_cuti_sakit_ranap_rs." </td>
                            </tr>";
            $tabel_capaian_efektif = "<tr>
                                <td> 1 </td>
                                <td> Cuti Alasan Penting </td>
                                <td> <span id='label_sakit'>".$jml_cuti_alasan_penting."</span> </td>
                                <td> ".$menit_per_hari."</td>
                                <td> ".$menit_cuti_alasan_penting." </td>
                            </tr>
                            
                            <tr>
                                <td> 2 </td>
                                <td> Isoman Covid-19 </td>
                                <td> <span id='label_dl'>".$jml_isoman."</span> </td>
                                <td> ".$menit_per_hari." </td>
                                <td> ".$menit_isoman." </td>
                            </tr>
                            <tr>
                                <td> 3 </td>
                                <td> Ranap Covid-19 </td>
                                <td> <span id='label_dl'>".$jml_ranapc19."</span> </td>
                                <td> ".$menit_per_hari." </td>
                                <td> ".$menit_ranapc19." </td>
                            </tr>
                            <tr>
                                <td> 4 </td>
                                <td> Cuti Tahunan </td>
                                <td> <span id='label_dl'>".$jml_cuti_tahunan."</span> </td>
                                <td> ".$menit_per_hari." </td>
                                <td> ".$menit_cuti_tahunan." </td>
                            </tr>
                            <tr>
                                <td> 5 </td>
                                <td> Cuti Bersalin </td>
                                <td> <span id='label_dl'>".$jml_cuti_bersalin."</span> </td>
                                <td> ".$menit_per_hari." </td>
                                <td> ".$menit_cuti_bersalin." </td>
                            </tr>
                            <tr>
                                <td> 6 </td>
                                <td> Cuti Besar </td>
                                <td> <span id='label_dl'>".$jml_cuti_besar."</span> </td>
                                <td> ".$menit_per_hari." </td>
                                <td> ".$menit_cuti_besar." </td>
                            </tr>
                            <tr>
                                <td> 7 </td>
                                <td> Dinas Luar Akhir </td>
                                <td> <span id='label_dl'>".$jml_dinas_luar_akhir."</span> </td>
                                <td> ".$menit_per_hari." </td>
                                <td> ".$menit_dinas_luar_akhir." </td>
                            </tr>
                            <tr>
                                <td> 8 </td>
                                <td> Dinas Luar Awal </td>
                                <td> <span id='label_dl'>".$jml_dinas_luar_awal."</span> </td>
                                <td> ".$menit_per_hari." </td>
                                <td> ".$menit_dinas_luar_awal." </td>
                            </tr>
                            <tr>
                                <td> 9 </td>
                                <td> Jari Tidak Terbaca </td>
                                <td> <span id='label_dl'>".$jml_tidak_terbaca."</span> </td>
                                <td> ".$menit_per_hari." </td>
                                <td> ".$menit_tidak_terbaca." </td>
                            </tr>
                            <tr>
                                <td> 10 </td>
                                <td> Dinas Luar Penuh </td>
                                <td> <span id='label_dl'>".$jml_dinas_luar_penuh."</span> </td>
                                <td> ".$menit_per_hari." </td>
                                <td> ".$menit_dinas_luar_penuh." </td>
                            </tr>
                            <tr>
                                <td> 11 </td>
                                <td> Cuti Bersalin Anak ke-3 </td>
                                <td> <span id='label_dl'>".$jml_cuti_bersalin_ak3."</span> </td>
                                <td> ".$menit_per_hari." </td>
                                <td> ".$menit_cuti_bersalin_ak3." </td>
                            </tr>
                            ";
            
            $res = array(
                'success' => true,
                'messages' => "Data found",
                'tabel_waktu' => $tabel_waktu,
                'tabel_capaian_efektif' => $tabel_capaian_efektif,
                'menit_kerja' => $menit_kerja,
                'total_pengurangan' => $total_pengurangan,
                'hasil_pengurangan' => $hasil_pengurangan,
                'total_penambahan' => $total_penambahan,
                'hasil_penambahan' => $hasil_penambahan,
                'nilai_capaian' => $nilai_capaian,
                'poin_capaian' => $poin_capaian,
                'persen_capaian' => number_format($persen_capaian,2),
                //'persen_kurang' => number_format($persen_kurang,2),
                'hari_kerja' => $hari_kerja,
                'persen_perilaku' => number_format($persen_perilaku,2),
                'persen_serapan' => $persen_serapan,
                'nilai_total' => $total_nilai,
                );
        }else{
            $tabel_waktu = "<tr>
                                <td colspan='5'> Data belum tersedia </td>
                            </tr>";
            $tabel_capaian_efektif = "<tr>
                                <td colspan='5'> Data belum tersedia </td>
                            </tr>";
            
            $res = array(
                'success' => true,
                'messages' => "Data found",
                'tabel_waktu' => $tabel_waktu,
                'tabel_capaian_efektif' => $tabel_capaian_efektif,
                'menit_kerja' => '0',
                'total_pengurangan' => '0',
                'hasil_pengurangan' => '0',
                'total_penambahan' => '0',
                'hasil_penambahan' => '0',
                'nilai_capaian' => '0',
                'poin_capaian' => '0',
                'persen_capaian' => '0',
                'hari_kerja' => '0',
                'persen_perilaku' => '0',
                'persen_serapan' => '0'
                );
        }
        
        echo json_encode($res);
    }
}