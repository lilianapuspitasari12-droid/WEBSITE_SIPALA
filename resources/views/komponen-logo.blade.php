{{-- resources/views/komponen-logo.blade.php --}}
@php $terang = $terang ?? false; @endphp
<a href="{{ url('/') }}" class="inline-flex items-center gap-3">
    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-biru">
        <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8" stroke-linecap="round"
             stroke-linejoin="round" class="h-6 w-6" aria-hidden="true">
            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
            <path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/>
        </svg>
    </span>
    <span>
        <span class="block text-xl font-bold leading-none tracking-tight {{ $terang ? 'text-white' : 'text-tinta' }}">SIPALA</span>
        <span class="mt-1 block text-[11px] font-medium tracking-[0.12em] text-tosca">PEMINJAMAN ALAT</span>
    </span>
</a>
