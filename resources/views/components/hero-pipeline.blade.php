{{-- E01: Hero Pipeline SVG — digambar sendiri via anime.js --}}
<div class="relative select-none" aria-hidden="true">
    <svg id="hero-pipeline-svg"
        viewBox="0 0 480 200"
        class="h-auto w-full max-w-md"
        fill="none" xmlns="http://www.w3.org/2000/svg">

        {{-- Track latar (abu) --}}
        <path d="M40 100 L180 100" stroke="rgb(34 53 102)" stroke-width="2"/>
        <path d="M180 100 L230 60" stroke="rgb(34 53 102)" stroke-width="2"/>
        <path d="M180 100 L230 140" stroke="rgb(34 53 102)" stroke-width="2"/>
        <path d="M230 60 L310 60" stroke="rgb(34 53 102)" stroke-width="2"/>
        <path d="M230 140 L310 140" stroke="rgb(34 53 102)" stroke-width="2"/>
        <path d="M310 60 L360 100" stroke="rgb(34 53 102)" stroke-width="2"/>
        <path d="M310 140 L360 100" stroke="rgb(34 53 102)" stroke-width="2"/>
        <path d="M360 100 L440 100" stroke="rgb(34 53 102)" stroke-width="2"/>

        {{-- Track aktif (signal) --}}
        <path class="pipeline-track" d="M40 100 L180 100" stroke="var(--color-signal)" stroke-width="2.5"/>
        <path class="pipeline-track" d="M180 100 L230 60" stroke="var(--color-signal)" stroke-width="2.5"/>
        <path class="pipeline-track" d="M180 100 L230 140" stroke="var(--color-signal)" stroke-width="2.5"/>
        <path class="pipeline-track" d="M230 60 L310 60" stroke="var(--color-signal)" stroke-width="2.5"/>
        <path class="pipeline-track" d="M230 140 L310 140" stroke="var(--color-signal)" stroke-width="2.5"/>
        <path class="pipeline-track" d="M310 60 L360 100" stroke="var(--color-signal)" stroke-width="2.5"/>
        <path class="pipeline-track" d="M310 140 L360 100" stroke="var(--color-signal)" stroke-width="2.5"/>
        <path class="pipeline-track" d="M360 100 L440 100" stroke="var(--color-mint)" stroke-width="2.5"/>

        {{-- Node awal: Daftar --}}
        <circle class="pipeline-node" cx="40" cy="100" r="8" fill="var(--color-signal)"/>
        {{-- Percabangan --}}
        <circle class="pipeline-node" cx="180" cy="100" r="6" fill="var(--color-signal)"/>
        {{-- Commit atas --}}
        <circle class="pipeline-node" cx="260" cy="60" r="5" fill="var(--color-signal)"/>
        {{-- Commit bawah --}}
        <circle class="pipeline-node" cx="260" cy="140" r="5" fill="var(--color-signal)"/>
        {{-- Merge --}}
        <circle class="pipeline-node" cx="360" cy="100" r="6" fill="var(--color-signal)"/>
        {{-- Node akhir: Diterima --}}
        <circle id="pipeline-final" cx="440" cy="100" r="10" fill="var(--color-mint)"/>
        <circle id="pipeline-pulse" cx="440" cy="100" r="10" fill="transparent" stroke="var(--color-mint)" stroke-width="2" opacity="0"/>

        {{-- Label --}}
        <text class="pipeline-label" x="40" y="122" fill="var(--color-mist)" font-size="10" text-anchor="middle">Daftar</text>
        <text class="pipeline-label" x="260" y="50" fill="var(--color-mist)" font-size="10" text-anchor="middle">Melamar</text>
        <text class="pipeline-label" x="260" y="165" fill="var(--color-mist)" font-size="10" text-anchor="middle">Interview</text>
        <text class="pipeline-label" x="440" y="122" fill="var(--color-mint)" font-size="10" font-weight="600" text-anchor="middle">Diterima</text>
    </svg>
</div>
