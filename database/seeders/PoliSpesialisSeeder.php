<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PoliSpesialisSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $dokterLama = [
            ['poli_id' => 5, 'nama' => 'dr. Hendra Kusuma, Sp.M',  'spesialis' => 'Spesialis Mata',        'nip' => 'NIP-MAT-001'],
            ['poli_id' => 4, 'nama' => 'dr. Sari Indah, Sp.A',     'spesialis' => 'Spesialis Anak',        'nip' => 'NIP-ANK-001'],
            ['poli_id' => 3, 'nama' => 'dr. Rina Wulandari, Sp.OG','spesialis' => 'Obstetri & Ginekologi', 'nip' => 'NIP-KIA-001'],
            ['poli_id' => 6, 'nama' => 'dr. Agus Prasetyo, Sp.PD', 'spesialis' => 'Penyakit Dalam',       'nip' => 'NIP-LNS-001'],
        ];
        $poliSpesialis = [
            ['kode'=>'PLI-THT','nama'=>'Poli THT',         'singkatan'=>'THT','keterangan'=>'Telinga, Hidung, dan Tenggorokan'],
            ['kode'=>'PLI-KUL','nama'=>'Poli Kulit',       'singkatan'=>'KUL','keterangan'=>'Kulit dan Kelamin'],
            ['kode'=>'PLI-JAN','nama'=>'Poli Jantung',     'singkatan'=>'JAN','keterangan'=>'Kardiologi dan Pembuluh Darah'],
            ['kode'=>'PLI-PAR','nama'=>'Poli Paru',        'singkatan'=>'PAR','keterangan'=>'Paru dan Pernapasan'],
            ['kode'=>'PLI-SAR','nama'=>'Poli Saraf',       'singkatan'=>'SAR','keterangan'=>'Neurologi'],
            ['kode'=>'PLI-BED','nama'=>'Poli Bedah',       'singkatan'=>'BED','keterangan'=>'Bedah Umum'],
            ['kode'=>'PLI-ORT','nama'=>'Poli Ortopedi',    'singkatan'=>'ORT','keterangan'=>'Tulang dan Sendi'],
            ['kode'=>'PLI-END','nama'=>'Poli Endokrin',    'singkatan'=>'END','keterangan'=>'Diabetes dan Hormon'],
            ['kode'=>'PLI-REH','nama'=>'Poli Rehabilitasi','singkatan'=>'REH','keterangan'=>'Rehabilitasi Medik'],
            ['kode'=>'PLI-JIW','nama'=>'Poli Jiwa',        'singkatan'=>'JIW','keterangan'=>'Kesehatan Jiwa'],
            ['kode'=>'PLI-KAN','nama'=>'Poli Kandungan',   'singkatan'=>'KAN','keterangan'=>'Kebidanan dan Kandungan'],
        ];
        $existingKode = DB::table('poli')->whereIn('kode',array_column($poliSpesialis,'kode'))->pluck('kode')->toArray();
        $poliToInsert = array_filter($poliSpesialis,fn($p)=>!in_array($p['kode'],$existingKode));
        foreach($poliToInsert as &$p){$p['is_active']=1;$p['created_at']=$now;$p['updated_at']=$now;}
        if(!empty($poliToInsert)) DB::table('poli')->insert(array_values($poliToInsert));
        $poliMap = DB::table('poli')->whereIn('kode',array_column($poliSpesialis,'kode'))->pluck('id','kode');
        $dokterBaru = [
            ['poli_id'=>$poliMap['PLI-THT']??null,'nama'=>'dr. Maya Sari, Sp.THT',          'spesialis'=>'THT-KL',                         'nip'=>'NIP-THT-001'],
            ['poli_id'=>$poliMap['PLI-KUL']??null,'nama'=>'dr. Fajar Nugroho, Sp.KK',       'spesialis'=>'Kulit & Kelamin',                'nip'=>'NIP-KUL-001'],
            ['poli_id'=>$poliMap['PLI-JAN']??null,'nama'=>'dr. Dewi Kartika, Sp.JP',        'spesialis'=>'Jantung & Pembuluh Darah',       'nip'=>'NIP-JAN-001'],
            ['poli_id'=>$poliMap['PLI-PAR']??null,'nama'=>'dr. Rizky Pratama, Sp.P',        'spesialis'=>'Pulmonologi',                    'nip'=>'NIP-PAR-001'],
            ['poli_id'=>$poliMap['PLI-SAR']??null,'nama'=>'dr. Lestari Putri, Sp.N',        'spesialis'=>'Neurologi',                      'nip'=>'NIP-SAR-001'],
            ['poli_id'=>$poliMap['PLI-BED']??null,'nama'=>'dr. Bambang Susilo, Sp.B',       'spesialis'=>'Bedah Umum',                     'nip'=>'NIP-BED-001'],
            ['poli_id'=>$poliMap['PLI-ORT']??null,'nama'=>'dr. Wahyu Hidayat, Sp.OT',       'spesialis'=>'Ortopedi & Traumatologi',        'nip'=>'NIP-ORT-001'],
            ['poli_id'=>$poliMap['PLI-END']??null,'nama'=>'dr. Nisa Rahmawati, Sp.PD-KEMD', 'spesialis'=>'Endokrinologi',                  'nip'=>'NIP-END-001'],
            ['poli_id'=>$poliMap['PLI-REH']??null,'nama'=>'dr. Taufik Ismail, Sp.KFR',      'spesialis'=>'Rehabilitasi Medik',             'nip'=>'NIP-REH-001'],
            ['poli_id'=>$poliMap['PLI-JIW']??null,'nama'=>'dr. Anisa Permata, Sp.KJ',       'spesialis'=>'Kesehatan Jiwa',                 'nip'=>'NIP-JIW-001'],
            ['poli_id'=>$poliMap['PLI-KAN']??null,'nama'=>'dr. Sri Mulyani, Sp.OG',         'spesialis'=>'Obstetri & Ginekologi',          'nip'=>'NIP-KAN-001'],
        ];
        $allDokter = array_merge($dokterLama,$dokterBaru);
        $existingNip = DB::table('dokter')->whereIn('nip',array_column($allDokter,'nip'))->pluck('nip')->toArray();
        $dokterToInsert = array_filter($allDokter,fn($d)=>!in_array($d['nip'],$existingNip)&&$d['poli_id']!==null);
        foreach($dokterToInsert as &$d){$d['user_id']=null;$d['str_number']=null;$d['no_hp']=null;$d['foto']=null;$d['is_active']=1;$d['created_at']=$now;$d['updated_at']=$now;}
        if(!empty($dokterToInsert)) DB::table('dokter')->insert(array_values($dokterToInsert));
        $dokterSesiList = DB::table('dokter')->whereIn('nip',array_column($dokterBaru,'nip'))->whereNotNull('poli_id')->get(['id','poli_id']);
        $tanggals=[];
        for($i=0;$i<=13;$i++){$tgl=now()->addDays($i);if($tgl->dayOfWeek===0)continue;$tanggals[]=$tgl->format('Y-m-d');if(count($tanggals)>=7)break;}
        $sesiInsert=[];
        foreach($dokterSesiList as $d){foreach($tanggals as $tgl){if(!DB::table('sesi_antrian')->where('poli_id',$d->poli_id)->where('tanggal',$tgl)->exists()){$sesiInsert[]=['poli_id'=>$d->poli_id,'dokter_id'=>$d->id,'tanggal'=>$tgl,'jam_buka'=>'08:00:00','jam_tutup'=>'12:00:00','kuota'=>20,'nomor_terakhir'=>0,'status'=>'aktif','dibuka_oleh'=>null,'ditutup_oleh'=>null,'created_at'=>$now,'updated_at'=>$now];}}}
        if(!empty($sesiInsert)) DB::table('sesi_antrian')->insert($sesiInsert);
        $this->command->info('✅ Selesai: '.count($poliToInsert).' poli, '.count($dokterToInsert).' dokter, '.count($sesiInsert).' sesi');
    }
}
