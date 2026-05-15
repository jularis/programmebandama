<?php

namespace App\Traits;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait GlobalStatus
{
	public static function changeStatus($id, $column = 'Status')
	{
		$modelName = get_class();

		$query     = $modelName::findOrFail($id);
		if ($query->status == Status::ENABLE) {
			$query->status = Status::DISABLE;
		} else {
			$query->status = Status::ENABLE;
		}
		$message       = $column . ' a été changé avec succès!';

		$query->save();
		$notify[] = ['success', $message];
		return back()->withNotify($notify);
	}

	public static function changeApprobation($id, $etat)
	{
		$modelName = get_class();

		$query     = $modelName::findOrFail($id);
		if ($etat == 1) {
			$query->approbation = 1;
			$message = "L'inspection a été approuvée avec succès!";
		} elseif($etat==2) {
			$query->approbation = 2;
			$message = "L'inspection a été non approuvée!";
		}else{
			$query->approbation = 3;
			$message = "Le producteur a été exclu!";
		} 
		$query->date_approbation = gmdate('d-m-Y');
		$query->save();
		$notify[] = ['success', $message];
		return back()->withNotify($notify);
	}


	public function statusBadge(): Attribute
	{
		return new Attribute(
			get: fn () => $this->badgeData(),
		);
	}

	public function statusEstim(): Attribute
	{
		return new Attribute(
			get: fn () => $this->estimData(),
		);
	}

	public function badgeData()
	{
		$html = '';
		if ($this->status == Status::ENABLE) {
			$html = '<span class="badge badge--success">' . trans('Activé') . '</span>';
		} else {
			$html = '<span><span class="badge badge--warning">' . trans('Désactivé') . '</span></span>';
		}
		return $html;
	}
	public function estimData()
	{
		$html = '';
		if ($this->status == Status::ENABLE) {
			$html = '<span class="badge badge--success">' . trans('Atteint') . '</span>';
		} else {
			$html = '<span><span class="badge badge--warning">' . trans('Non atteint') . '</span></span>';
		}
		return $html;
	}

	public function scopeActive($query)
	{
		return $query->where('status', Status::ENABLE);
	}

	public function scopeInactive($query)
	{
		return $query->where('status', Status::DISABLE);
	}
}