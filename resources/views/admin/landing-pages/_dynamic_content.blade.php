<!-- Benefits Section -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-star text-warning me-2"></i> Product Benefits
            </h5>
            <button type="button" class="btn btn-sm btn-primary" onclick="addBenefit()">
                <i class="bi bi-plus-circle"></i> Add Benefit
            </button>
        </div>
    </div>
    <div class="card-body p-4">
        <div id="benefits-container">
            @if(isset($landingPage) && $landingPage->benefits && count($landingPage->benefits) > 0)
                @foreach($landingPage->benefits as $index => $benefit)
                <div class="benefit-item border rounded p-3 mb-3" data-index="{{ $index }}">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong>Benefit #{{ $index + 1 }}</strong>
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeBenefit(this)">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label small">Title</label>
                            <input type="text" class="form-control" name="benefits[{{ $index }}][title]" value="{{ $benefit['title'] ?? '' }}" placeholder="e.g., Instant Setup">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Icon (Bootstrap Icon)</label>
                            <input type="text" class="form-control" name="benefits[{{ $index }}][icon]" value="{{ $benefit['icon'] ?? 'check-circle-fill' }}" placeholder="rocket-takeoff-fill">
                            <small class="text-muted">Just the icon name, without 'bi-' prefix</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label small">Description</label>
                            <textarea class="form-control" name="benefits[{{ $index }}][description]" rows="2" placeholder="Describe this benefit...">{{ $benefit['description'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
                @endforeach
            @endif
        </div>
        <small class="text-muted">
            <i class="bi bi-info-circle"></i> Benefits will be displayed on the landing page. 
            <a href="https://icons.getbootstrap.com/" target="_blank" class="text-primary">Browse Bootstrap Icons</a>
            <br>
            <strong>Examples:</strong> rocket-takeoff-fill, shield-check, lightning-charge-fill, heart-fill, award-fill
        </small>
    </div>
</div>

<!-- Testimonials Section -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-chat-quote text-info me-2"></i> Customer Testimonials
            </h5>
            <button type="button" class="btn btn-sm btn-primary" onclick="addTestimonial()">
                <i class="bi bi-plus-circle"></i> Add Testimonial
            </button>
        </div>
    </div>
    <div class="card-body p-4">
        <div id="testimonials-container">
            @if(isset($landingPage) && $landingPage->testimonials && count($landingPage->testimonials) > 0)
                @foreach($landingPage->testimonials as $index => $testimonial)
                <div class="testimonial-item border rounded p-3 mb-3" data-index="{{ $index }}">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong>Testimonial #{{ $index + 1 }}</strong>
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeTestimonial(this)">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label small">Customer Name</label>
                            <input type="text" class="form-control" name="testimonials[{{ $index }}][name]" value="{{ $testimonial['name'] ?? '' }}" placeholder="e.g., John Doe">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Position/Company</label>
                            <input type="text" class="form-control" name="testimonials[{{ $index }}][position]" value="{{ $testimonial['position'] ?? '' }}" placeholder="e.g., CEO at Company">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small">Rating (1-5)</label>
                            <select class="form-select" name="testimonials[{{ $index }}][rating]">
                                @for($i = 5; $i >= 1; $i--)
                                    <option value="{{ $i }}" {{ ($testimonial['rating'] ?? 5) == $i ? 'selected' : '' }}>{{ $i }} Stars</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small">Testimonial Content</label>
                            <textarea class="form-control" name="testimonials[{{ $index }}][content]" rows="3" placeholder="What did the customer say...">{{ $testimonial['content'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
                @endforeach
            @endif
        </div>
        <small class="text-muted">
            <i class="bi bi-info-circle"></i> Testimonials help build trust with potential customers.
        </small>
    </div>
</div>
