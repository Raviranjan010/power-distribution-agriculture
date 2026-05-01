@extends('layouts.app')

@section('content')
<div class="mb-8">
    <h2 class="text-3xl font-bold text-theme-heading mb-1">Help & Support</h2>
    <p class="text-sm text-theme-text">Find answers to common questions and get assistance</p>
</div>

{{-- Quick Actions --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <a href="{{ route('farmer.complaints') }}" class="utilitarian-card p-5 hover:border-rose-500/50 transition-colors group">
        <div class="w-10 h-10 rounded-lg bg-rose-500/20 text-rose-400 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
            <i class="fa-regular fa-comment-dots text-lg"></i>
        </div>
        <h3 class="text-sm font-bold text-theme-heading mb-1">File a Complaint</h3>
        <p class="text-xs text-theme-text">Report power supply issues, billing errors, or equipment problems.</p>
    </a>
    <a href="{{ route('farmer.bills') }}" class="utilitarian-card p-5 hover:border-amber-500/50 transition-colors group">
        <div class="w-10 h-10 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
            <i class="fa-solid fa-file-invoice text-lg"></i>
        </div>
        <h3 class="text-sm font-bold text-theme-heading mb-1">Pay Your Bills</h3>
        <p class="text-xs text-theme-text">View outstanding bills and make online payments instantly.</p>
    </a>
    <a href="{{ route('farmer.connections') }}" class="utilitarian-card p-5 hover:border-emerald-500/50 transition-colors group">
        <div class="w-10 h-10 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
            <i class="fa-solid fa-plug text-lg"></i>
        </div>
        <h3 class="text-sm font-bold text-theme-heading mb-1">New Connection</h3>
        <p class="text-xs text-theme-text">Apply for a new agricultural power connection for your farm.</p>
    </a>
</div>

{{-- Contact Information --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="utilitarian-card p-6">
        <h3 class="text-lg font-bold text-theme-heading mb-4 flex items-center gap-2">
            <i class="fa-solid fa-phone text-theme-accent"></i> Contact Information
        </h3>
        <div class="space-y-4">
            <div class="flex items-start gap-4 p-3 rounded-lg bg-theme-bg/50 border border-theme-border">
                <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-headset"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-theme-heading">24×7 Helpline</p>
                    <p class="text-xs text-theme-text">1912 (Toll Free)</p>
                    <p class="text-xs text-theme-text">Available round the clock for emergencies</p>
                </div>
            </div>
            <div class="flex items-start gap-4 p-3 rounded-lg bg-theme-bg/50 border border-theme-border">
                <div class="w-8 h-8 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-building"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-theme-heading">SDO Office</p>
                    <p class="text-xs text-theme-text">Mon–Fri: 9:00 AM – 5:00 PM</p>
                    <p class="text-xs text-theme-text">Visit your local Sub-Division Office for in-person assistance</p>
                </div>
            </div>
            <div class="flex items-start gap-4 p-3 rounded-lg bg-theme-bg/50 border border-theme-border">
                <div class="w-8 h-8 rounded-lg bg-indigo-500/20 text-indigo-400 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-envelope"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-theme-heading">Email Support</p>
                    <p class="text-xs text-theme-text">support@agricpower.gov.in</p>
                    <p class="text-xs text-theme-text">Response within 2 business days</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Tariff Rates --}}
    <div class="utilitarian-card p-6">
        <h3 class="text-lg font-bold text-theme-heading mb-4 flex items-center gap-2">
            <i class="fa-solid fa-indian-rupee-sign text-theme-accent"></i> Agricultural Tariff Rates
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="text-left text-[10px] text-theme-text font-bold tracking-widest uppercase border-b border-theme-border">
                        <th class="pb-2 pr-3">Category</th>
                        <th class="pb-2 pr-3 text-right">Rate/Unit</th>
                        <th class="pb-2 text-right">Fixed/kW</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $tariffs = \App\Models\TariffCategory::where('is_active', true)->orderBy('applicable_to')->get();
                    @endphp
                    @forelse($tariffs as $tariff)
                        <tr class="border-b border-theme-border/30">
                            <td class="py-2.5 pr-3">
                                <p class="font-bold text-theme-heading">{{ $tariff->name }}</p>
                                <p class="text-[10px] text-theme-text">{{ ucfirst($tariff->applicable_to) }}</p>
                            </td>
                            <td class="py-2.5 pr-3 text-right font-bold text-theme-heading">₹{{ number_format($tariff->rate_per_unit, 2) }}</td>
                            <td class="py-2.5 text-right font-bold text-theme-heading">₹{{ number_format($tariff->fixed_charge_per_kw, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-4 text-center text-theme-text">No tariff rates available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <p class="text-[10px] text-theme-text mt-3">* Rates are subject to revision by the State Electricity Regulatory Commission.</p>
    </div>
</div>

{{-- FAQ Section --}}
<div class="utilitarian-card p-6">
    <h3 class="text-lg font-bold text-theme-heading mb-6 flex items-center gap-2">
        <i class="fa-regular fa-circle-question text-theme-accent"></i> Frequently Asked Questions
    </h3>

    <div class="space-y-3" id="faqContainer">
        @php
            $faqs = [
                ['q' => 'How do I apply for a new agricultural power connection?', 'a' => 'Go to the Connections page and click "Request New". Fill in the connection type, field name, and required load (kW). Your application will be reviewed by the local SDO within 7-10 working days.'],
                ['q' => 'How is my electricity bill calculated?', 'a' => 'Your bill consists of three components: Energy Charges (units consumed × rate per unit), Fixed Charges (sanctioned load in kW × fixed charge per kW), and applicable taxes (5% of total). Subsidy discounts are applied automatically if you have an approved scheme.'],
                ['q' => 'What should I do if I face frequent voltage fluctuations?', 'a' => 'File a complaint under "Voltage Fluctuation" category from the Complaints page. Mention the time and frequency of fluctuations. The lineman will be assigned to inspect your connection and the transformer feeding your area.'],
                ['q' => 'How long does a new connection approval take?', 'a' => 'Once submitted, your connection request is reviewed by the SDO of your zone. Typically, approval takes 7-15 working days. After approval, the lineman will visit for meter installation within 3-5 working days.'],
                ['q' => 'How can I check my meter reading?', 'a' => 'All verified meter readings appear on your Connections page under each connection. The lineman records readings monthly. If you notice a discrepancy, file a "Meter Fault" complaint.'],
                ['q' => 'What government subsidies are available for farmers?', 'a' => 'Visit the Subsidies page to see all active schemes. Common schemes include PM-KUSUM for solar pump subsidies and state-level flat-rate waivers. You can apply directly from the portal and the SDO will process your application.'],
                ['q' => 'How do I pay my electricity bill online?', 'a' => 'Go to Bills & Payments page. Find your pending bill and click the "Pay Now" button. The payment is processed instantly and your bill status updates to "Paid" with a transaction ID for your records.'],
                ['q' => 'What to do in case of a power outage?', 'a' => 'For emergency outages, call the 24×7 helpline at 1912. For non-emergency reporting, file a complaint under "No Supply" category. Include the approximate time the outage started and the area affected.'],
            ];
        @endphp

        @foreach($faqs as $index => $faq)
            <div class="border border-theme-border rounded-lg overflow-hidden">
                <button onclick="toggleFaq({{ $index }})"
                    class="w-full flex justify-between items-center p-4 text-left hover:bg-theme-border/30 transition-colors"
                    id="faqBtn{{ $index }}">
                    <span class="text-sm font-bold text-theme-heading pr-4">{{ $faq['q'] }}</span>
                    <i class="fa-solid fa-chevron-down text-theme-text text-xs transition-transform" id="faqIcon{{ $index }}"></i>
                </button>
                <div class="hidden px-4 pb-4" id="faqAnswer{{ $index }}">
                    <p class="text-xs text-theme-text leading-relaxed">{{ $faq['a'] }}</p>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- Emergency Banner --}}
<div class="mt-6 utilitarian-card p-4 border-l-4 border-l-rose-500 flex items-center gap-4">
    <div class="w-10 h-10 rounded-lg bg-rose-500/20 text-rose-400 flex items-center justify-center flex-shrink-0">
        <i class="fa-solid fa-triangle-exclamation text-lg"></i>
    </div>
    <div>
        <h4 class="text-sm font-bold text-theme-heading">Emergency? Report downed power lines immediately.</h4>
        <p class="text-xs text-theme-text">Call <span class="text-rose-400 font-bold">1912</span> (Toll Free) or <span class="text-rose-400 font-bold">112</span> for life-threatening emergencies. Do not approach downed lines.</p>
    </div>
</div>

<script>
function toggleFaq(index) {
    const answer = document.getElementById('faqAnswer' + index);
    const icon = document.getElementById('faqIcon' + index);
    const isHidden = answer.classList.contains('hidden');

    // Close all
    document.querySelectorAll('[id^="faqAnswer"]').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('[id^="faqIcon"]').forEach(el => el.style.transform = 'rotate(0deg)');

    if (isHidden) {
        answer.classList.remove('hidden');
        icon.style.transform = 'rotate(180deg)';
    }
}
</script>
@endsection
