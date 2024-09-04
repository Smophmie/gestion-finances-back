<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\BelongsTo;

/**
 * @OA\Schema(
 *     schema="Transaction",
 *     type="object",
 *     title="Transaction",
 *     description="Details of a financial transaction",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         example=1,
 *         description="Unique identifier for the transaction"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         example="Transaction 1",
 *         description="The name or description of the transaction"
 *     ),
 *     @OA\Property(
 *         property="user_id",
 *         type="integer",
 *         example=1,
 *         description="The ID of the user associated with the transaction"
 *     ),
 *     @OA\Property(
 *         property="type",
 *         type="string",
 *         example="earning",
 *         description="The type of the transaction (e.g., earning, expense)"
 *     ),
 *     @OA\Property(
 *         property="amount",
 *         type="number",
 *         format="float",
 *         example=12.00,
 *         description="The amount of the transaction"
 *     ),
 *     @OA\Property(
 *         property="date",
 *         type="string",
 *         format="date",
 *         example="2024-12-08",
 *         description="The date of the transaction"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         example="2024-09-04T13:19:47Z",
 *         description="The date and time when the transaction was created"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         example="2024-09-04T13:19:47Z",
 *         description="The date and time when the transaction was last updated"
 *     )
 * )
 */
class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'type',
        'amount',
        'date'
    ];

}
