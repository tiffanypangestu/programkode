@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')
    <div
                                        class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 mb-3">
                                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <h3 class="font-semibold text-gray-900">Belum ada transaksi penjualan</h3>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Silakan input transaksi baru untuk melihat daftar di sini.
                                    </p>
                                    <a href="{{ route('penjualan.create') }}"
                                        class="mt-4 inline-block rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                                        + Input Penjualan Baru
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($transaksi->hasPages())
                <div class="border-t border-gray-200 px-5 py-4">
                    {{ $transaksi->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
