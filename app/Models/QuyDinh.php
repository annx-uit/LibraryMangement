<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuyDinh extends Model
{
    protected $table = 'QUYDINH';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    
    protected $fillable = [
        'TenQuyDinh',
        'GiaTri',
    ];

    // Constants for regulation codes
    const MIN_AGE = 'MIN_AGE';
    const MAX_AGE = 'MAX_AGE';
    const CARD_VALIDITY_MONTHS = 'CARD_VALIDITY_MONTHS';
    const MAX_BOOKS_PER_BORROW = 'MAX_BOOKS_PER_BORROW';
    const BORROW_DURATION_DAYS = 'BORROW_DURATION_DAYS';
    const BOOK_PUBLICATION_YEARS = 'BOOK_PUBLICATION_YEARS';

    /**
     * Get regulation value by code name
     */
    public static function getValue($name, $default = null)
    {
        $regulation = static::where('TenQuyDinh', $name)->first();
        return $regulation ? $regulation->GiaTri : $default;
    }

    /**
     * Set regulation value
     */
    public static function setValue($name, $value)
    {
        return static::updateOrCreate(
            ['TenQuyDinh' => $name],
            ['GiaTri' => $value]
        );
    }

    /**
     * Get all regulations as key-value pairs
     */
    public static function getAllValues()
    {
        return static::pluck('GiaTri', 'TenQuyDinh')->toArray();
    }

    /**
     * Helper methods for specific regulations
     */
    public static function getMinAge()
    {
        return (int) static::getValue('Tuổi tối thiểu độc giả', 18);
    }

    public static function getMaxAge()
    {
        return (int) static::getValue('Tuổi tối đa độc giả', 55);
    }

    public static function getCardValidityMonths()
    {
        return (int) static::getValue('Thời hạn thẻ độc giả (tháng)', 6);
    }

    public static function getMaxBooksPerBorrow()
    {
        return (int) static::getValue('Số lượng sách tối đa mượn cùng lúc', 5);
    }

    public static function getBorrowDurationDays()
    {
        return (int) static::getValue('Số ngày mượn tối đa', 30);
    }

    public static function getBookPublicationYears()
    {
        return (int) static::getValue('Số năm xuất bản sách được chấp nhận', 8);
    }
}
