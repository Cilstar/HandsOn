import { useState, createContext, useContext } from 'react'
import { BrowserRouter as Router, Routes, Route, Link, useLocation, useNavigate, Navigate } from 'react-router-dom'
import { motion, AnimatePresence } from 'framer-motion'
import { MapContainer, TileLayer, Marker, Popup } from 'react-leaflet'
import L from 'leaflet'
import {
  Home, Search, User, Calendar, Users, BarChart3, Menu, X,
  Star, MapPin, Clock, CheckCircle, Wrench, Zap, Droplets,
  Paintbrush, UserCheck, Briefcase, ArrowRight, Bell, ChevronRight,
  LogIn, LogOut, Download, Plus, Settings, DollarSign, LogOut as LogoutIcon,
  Map
} from 'lucide-react'

// Fix Leaflet default marker icon
delete L.Icon.Default.prototype._getIconUrl
L.Icon.Default.mergeOptions({
  iconRetinaUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-icon-2x.png',
  iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-icon.png',
  shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
})

// Custom green icon for available workers
const availableIcon = new L.Icon({
  iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
  shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
  iconSize: [25, 41],
  iconAnchor: [12, 41],
  popupAnchor: [1, -34],
  shadowSize: [41, 41]
})

// Custom gray icon for unavailable workers
const busyIcon = new L.Icon({
  iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-grey.png',
  shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
  iconSize: [25, 41],
  iconAnchor: [12, 41],
  popupAnchor: [1, -34],
  shadowSize: [41, 41]
})

// Auth Context
const AuthContext = createContext(null)

// Format currency to Kenyan Shillings
const formatCurrency = (amount) => {
  return `KSh ${Number(amount).toLocaleString('en-KE')}`
}

// Service categories data
const services = [
  { id: 'cleaner', name: 'Cleaner', icon: '🧹', color: 'from-cyan-400 to-cyan-600', bgColor: 'bg-cyan-100', description: 'Deep cleaning & regular housekeeping' },
  { id: 'painter', name: 'Painter', icon: '🎨', color: 'from-violet-400 to-violet-600', bgColor: 'bg-violet-100', description: 'Interior & exterior painting' },
  { id: 'technician', name: 'Technician', icon: '🔧', color: 'from-amber-400 to-amber-600', bgColor: 'bg-amber-100', description: 'Appliance & equipment repair' },
  { id: 'electrician', name: 'Electrician', icon: '⚡', color: 'from-yellow-400 to-yellow-600', bgColor: 'bg-yellow-100', description: 'Electrical installations & repairs' },
  { id: 'plumber', name: 'Plumber', icon: '🚿', color: 'from-neon-400 to-neon-600', bgColor: 'bg-neon-100', description: 'Pipe fitting & water systems' },
]

// Mock worker data with Kenyan names
const mockWorkers = [
  { id: 1, name: 'John Kiprotich', profession: 'Electrician', rating: 4.9, reviews: 127, distance: '0.8 km', price: 5000, available: true, phone: '+254712345678', location: 'Nairobi', lat: -1.2921, lng: 36.8219 },
  { id: 2, name: 'Sarah Wanjiku', profession: 'Cleaner', rating: 4.8, reviews: 89, distance: '1.2 km', price: 3500, available: true, phone: '+254723456789', location: 'Kisumu', lat: -0.1022, lng: 34.7617 },
  { id: 3, name: 'Michael Ochieng', profession: 'Plumber', rating: 4.7, reviews: 156, distance: '2.1 km', price: 5500, available: false, phone: '+254734567890', location: 'Mombasa', lat: -4.0435, lng: 39.6682 },
  { id: 4, name: 'Emily Atieno', profession: 'Painter', rating: 4.9, reviews: 203, distance: '1.5 km', price: 4000, available: true, phone: '+254745678901', location: 'Nairobi', lat: -1.2864, lng: 36.7679 },
  { id: 6, name: 'Grace Nkirote', profession: 'Technician', rating: 4.8, reviews: 112, distance: '3.0 km', price: 5000, available: true, phone: '+254767890123', location: 'Eldoret', lat: 0.5143, lng: 35.2698 },
]

// Mock reviews with Kenyan names
const mockReviews = [
  { id: 1, user: 'Alex Kamau', rating: 5, date: '2 days ago', comment: 'Excellent work! Very professional and on time. Highly recommend!' },
  { id: 2, user: 'Maryanne Moraa', rating: 5, date: '1 week ago', comment: 'Great experience. The work was completed perfectly and the price was fair.' },
  { id: 3, user: 'James Otieno', rating: 4, date: '2 weeks ago', comment: 'Good service overall. Would use again for future needs.' },
]

// Avatar Placeholder Component
function Avatar({ name, size = 'md', className = '' }) {
  const initials = name.split(' ').map(n => n[0]).join('').slice(0, 2)
  const sizeClasses = {
    sm: 'w-8 h-8 text-xs',
    md: 'w-10 h-10 text-sm',
    lg: 'w-16 h-16 text-lg',
    xl: 'w-32 h-32 text-2xl'
  }
  
  return (
    <div className={`${sizeClasses[size]} rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-semibold ${className}`}>
      {initials}
    </div>
  )
}

// Auth Provider
function AuthProvider({ children }) {
  const [user, setUser] = useState(null)
  const [loading, setLoading] = useState(false)

  const login = async (email, password, role) => {
    setLoading(true)
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1000))
    
    if (role === 'customer') {
      setUser({ id: 1, name: 'John Doe', email, role: 'customer', phone: '+254700000001' })
    } else if (role === 'worker') {
      setUser({ id: 2, name: 'John Kiprotich', email, role: 'worker', workerId: 1, phone: '+254712345678' })
    } else if (role === 'admin') {
      setUser({ id: 3, name: 'Admin User', email, role: 'admin', phone: '+254700000003' })
    }
    setLoading(false)
    return true
  }

  const logout = () => {
    setUser(null)
  }

  return (
    <AuthContext.Provider value={{ user, login, logout, loading }}>
      {children}
    </AuthContext.Provider>
  )
}

// Use Auth hook
function useAuth() {
  const context = useContext(AuthContext)
  if (!context) {
    throw new Error('useAuth must be used within an AuthProvider')
  }
  return context
}

// Protected Route Component
function ProtectedRoute({ children, allowedRoles }) {
  const { user } = useAuth()
  const location = useLocation()

  if (!user) {
    return <Navigate to="/login" state={{ from: location }} replace />
  }

  if (allowedRoles && !allowedRoles.includes(user.role)) {
    return <Navigate to="/" replace />
  }

  return children
}

// Star Rating Component
function StarRating({ rating, size = 'md' }) {
  const sizeClass = size === 'sm' ? 'w-4 h-4' : size === 'lg' ? 'w-6 h-6' : 'w-5 h-5'
  
  return (
    <div className="star-rating">
      {[1, 2, 3, 4, 5].map((star) => (
        <motion.svg
          key={star}
          whileHover={{ scale: 1.2 }}
          className={`${sizeClass} ${star <= rating ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300'}`}
          viewBox="0 0 24 24"
        >
          <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
        </motion.svg>
      ))}
    </div>
  )
}

// Navigation Component
function Navbar() {
  const [isOpen, setIsOpen] = useState(false)
  const location = useLocation()
  const { user, logout } = useAuth()

  const navLinks = [
    { path: '/', label: 'Home', icon: Home },
    { path: '/workers', label: 'Find Workers', icon: Search },
    { path: '/dashboard', label: 'Dashboard', icon: User, roles: ['customer'] },
    { path: '/worker-dashboard', label: 'Worker Portal', icon: Briefcase, roles: ['worker'] },
    { path: '/admin', label: 'Admin', icon: BarChart3, roles: ['admin'] },
  ].filter(link => !link.roles || (user && link.roles.includes(user.role)) || !user)

  return (
    <nav className="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-lg border-b border-gray-100">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex items-center justify-between h-16">
          <Link to="/" className="flex items-center gap-2">
            <div className="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center shadow-lg shadow-primary-500/30">
              <span className="text-white font-bold text-xl">H</span>
            </div>
            <span className="font-display font-bold text-xl text-gray-800">HandsOn</span>
          </Link>

          <div className="hidden md:flex items-center gap-8">
            {user ? (
              <>
                {navLinks.map((link) => (
                  <Link
                    key={link.path}
                    to={link.path}
                    className={`nav-link flex items-center gap-2 ${location.pathname === link.path ? 'nav-link-active' : ''}`}
                  >
                    <link.icon className="w-4 h-4" />
                    {link.label}
                  </Link>
                ))}
                <div className="flex items-center gap-4">
                  <span className="text-sm text-gray-600">Hi, {user.name}</span>
                  <button
                    onClick={logout}
                    className="flex items-center gap-2 text-red-600 hover:text-red-700"
                  >
                    <LogOut className="w-4 h-4" />
                    Logout
                  </button>
                </div>
              </>
            ) : (
              <>
                <Link to="/login" className="nav-link">Login</Link>
                <Link to="/login" className="btn-primary text-sm py-2">Get Started</Link>
              </>
            )}
          </div>

          <button
            onClick={() => setIsOpen(!isOpen)}
            className="md:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors"
          >
            {isOpen ? <X className="w-6 h-6" /> : <Menu className="w-6 h-6" />}
          </button>
        </div>
      </div>

      <AnimatePresence>
        {isOpen && (
          <motion.div
            initial={{ opacity: 0, height: 0 }}
            animate={{ opacity: 1, height: 'auto' }}
            exit={{ opacity: 0, height: 0 }}
            className="md:hidden bg-white border-t border-gray-100"
          >
            <div className="px-4 py-4 space-y-2">
              {user ? (
                <>
                  {navLinks.map((link) => (
                    <Link
                      key={link.path}
                      to={link.path}
                      onClick={() => setIsOpen(false)}
                      className={`flex items-center gap-3 px-4 py-3 rounded-xl transition-colors ${
                        location.pathname === link.path
                          ? 'bg-primary-50 text-primary-600'
                          : 'text-gray-600 hover:bg-gray-50'
                      }`}
                    >
                      <link.icon className="w-5 h-5" />
                      {link.label}
                    </Link>
                  ))}
                  <button
                    onClick={() => { logout(); setIsOpen(false); }}
                    className="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors w-full text-left text-red-600"
                  >
                    <LogOut className="w-5 h-5" />
                    Logout
                  </button>
                </>
              ) : (
                <Link
                  to="/login"
                  onClick={() => setIsOpen(false)}
                  className="flex items-center gap-3 px-4 py-3 rounded-xl bg-primary-50 text-primary-600"
                >
                  <LogIn className="w-5 h-5" />
                  Login / Sign Up
                </Link>
              )}
            </div>
          </motion.div>
        )}
      </AnimatePresence>
    </nav>
  )
}

// Login Page
function LoginPage() {
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [role, setRole] = useState('customer')
  const { login, loading } = useAuth()
  const location = useLocation()
  const navigate = useNavigate()
  
  const from = location.state?.from?.pathname || '/'

  const handleSubmit = async (e) => {
    e.preventDefault()
    const success = await login(email, password, role)
    if (success) {
      navigate(from)
    }
  }

  return (
    <div className="min-h-screen pt-16 bg-mesh flex items-center justify-center px-4">
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        className="w-full max-w-md"
      >
        <div className="glass-card p-8">
          <div className="text-center mb-8">
            <div className="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-primary-500/30">
              <span className="text-white font-bold text-2xl">H</span>
            </div>
            <h1 className="font-display text-2xl font-bold text-gray-900">Welcome Back</h1>
            <p className="text-gray-600">Sign in to your HandsOn account</p>
          </div>

          <form onSubmit={handleSubmit} className="space-y-6">
            {/* Role Selection */}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-3">Select role</label>
              <div className="grid grid-cols-3 gap-3">
                {[
                  { value: 'customer', label: 'Customer', icon: User },
                  { value: 'worker', label: 'Worker', icon: Wrench },
                ].map((option) => (
                  <button
                    key={option.value}
                    type="button"
                    onClick={() => setRole(option.value)}
                    className={`p-3 rounded-xl border-2 transition-all ${
                      role === option.value
                        ? 'border-primary-500 bg-primary-50 text-primary-600'
                        : 'border-gray-200 text-gray-600 hover:border-gray-300'
                    }`}
                  >
                    <option.icon className="w-6 h-6 mx-auto mb-1" />
                    <span className="text-sm font-medium">{option.label}</span>
                  </button>
                ))}
              </div>
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Email</label>
              <input
                type="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                className="input-field"
                placeholder="you@example.com"
                required
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Password</label>
              <input
                type="password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                className="input-field"
                placeholder="••••••••"
                required
              />
            </div>

            <button
              type="submit"
              disabled={loading}
              className="btn-primary w-full flex items-center justify-center gap-2"
            >
              {loading ? (
                <div className="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin" />
              ) : (
                <>
                  <LogIn className="w-5 h-5" />
                  Sign In
                </>
              )}
            </button>
          </form>

          <div className="mt-6 text-center">
            <p className="text-gray-600">
              Don't have an account?{' '}
              <Link to="/register" className="text-primary-600 font-medium hover:underline">
                Sign up
              </Link>
            </p>
          </div>
        </div>
      </motion.div>
    </div>
  )
}

// Registration Page
function RegisterPage() {
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    phone: '',
    password: '',
    confirmPassword: '',
    role: 'customer',
    profession: '',
    location: '',
  })
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState('')
  const { login } = useAuth()
  const navigate = useNavigate()

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value })
  }

  const handleSubmit = async (e) => {
    e.preventDefault()
    setError('')
    
    if (formData.password !== formData.confirmPassword) {
      setError('Passwords do not match')
      return
    }

    if (formData.password.length < 6) {
      setError('Password must be at least 6 characters')
      return
    }

    setLoading(true)
    await new Promise(resolve => setTimeout(resolve, 1000))
    await login(formData.email, formData.password, formData.role)
    setLoading(false)
    
    if (formData.role === 'worker') {
      navigate('/worker-dashboard')
    } else {
      navigate('/dashboard')
    }
  }

  return (
    <div className="min-h-screen pt-16 bg-mesh flex items-center justify-center px-4 py-8">
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        className="w-full max-w-lg"
      >
        <div className="glass-card p-8">
          <div className="text-center mb-8">
            <div className="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-primary-500/30">
              <span className="text-white font-bold text-2xl">H</span>
            </div>
            <h1 className="font-display text-2xl font-bold text-gray-900">Create Account</h1>
            <p className="text-gray-600">Join HandsOn</p>
          </div>

          {error && (
            <div className="mb-4 p-3 bg-red-50 text-red-600 rounded-xl text-sm">{error}</div>
          )}

          <form onSubmit={handleSubmit} className="space-y-4">
            <div className="grid grid-cols-2 gap-3">
                <button
                  type="button"
                  onClick={() => setFormData({ ...formData, role: 'customer', profession: '' })}
                  className={`p-4 rounded-xl border-2 transition-all ${
                    formData.role === 'customer' ? 'border-primary-500 bg-primary-50' : 'border-gray-200'
                  }`}
                >
                  <User className="w-8 h-8 mx-auto mb-2 text-gray-600" />
                  <span className="font-medium text-gray-700">Find Services</span>
                </button>
                <button
                  type="button"
                  onClick={() => setFormData({ ...formData, role: 'worker', profession: 'Electrician' })}
                  className={`p-4 rounded-xl border-2 transition-all ${
                    formData.role === 'worker' ? 'border-primary-500 bg-primary-50' : 'border-gray-200'
                  }`}
                >
                  <Wrench className="w-8 h-8 mx-auto mb-2 text-gray-600" />
                  <span className="font-medium text-gray-700">Provide Services</span>
                </button>
              </div>

            <div className="grid grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                <input type="text" name="name" value={formData.name} onChange={handleChange} className="input-field" placeholder="John" required />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                <input type="text" name="lastName" value={formData.lastName || ''} onChange={handleChange} className="input-field" placeholder="Doe" required />
              </div>
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Email</label>
              <input type="email" name="email" value={formData.email} onChange={handleChange} className="input-field" placeholder="you@example.com" required />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Phone</label>
              <input type="tel" name="phone" value={formData.phone} onChange={handleChange} className="input-field" placeholder="+254 700 000 000" required />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Location</label>
              <select name="location" value={formData.location} onChange={handleChange} className="input-field" required>
                <option value="">Select city</option>
                <option value="Nairobi">Nairobi</option>
                <option value="Mombasa">Mombasa</option>
                <option value="Kisumu">Kisumu</option>
                <option value="Nakuru">Nakuru</option>
                <option value="Eldoret">Eldoret</option>
                <option value="Other">Other</option>
              </select>
            </div>

            {formData.role === 'worker' && (
              <>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">Service Category</label>
                  <select name="profession" value={formData.profession} onChange={handleChange} className="input-field" required>
                    <option value="">Select service</option>
                    <option value="Electrician">Electrician</option>
                    <option value="Plumber">Plumber</option>
                    <option value="Painter">Painter</option>
                    <option value="Cleaner">Cleaner</option>
                    <option value="Technician">Technician</option>
                  </select>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">Hourly Rate (KSh)</label>
                  <input type="number" name="rate" value={formData.rate || ''} onChange={handleChange} className="input-field" placeholder="5000" min="500" required />
                </div>
              </>
            )}

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Password</label>
              <input type="password" name="password" value={formData.password} onChange={handleChange} className="input-field" placeholder="At least 6 characters" required />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
              <input type="password" name="confirmPassword" value={formData.confirmPassword} onChange={handleChange} className="input-field" placeholder="Confirm password" required />
            </div>

            <button type="submit" disabled={loading} className="btn-primary w-full">
              {loading ? 'Creating Account...' : 'Create Account'}
            </button>
          </form>

          <div className="mt-6 text-center">
            <p className="text-gray-600">Already have an account? <Link to="/login" className="text-primary-600 font-medium hover:underline">Sign in</Link></p>
          </div>
        </div>
      </motion.div>
    </div>
  )
}

// Home Page Component
function HomePage() {
  const [selectedService, setSelectedService] = useState(null)
  const [requestStatus, setRequestStatus] = useState('idle') // idle, searching, found, enroute, arrived
  const [foundWorker, setFoundWorker] = useState(null)

  const handleRequestService = (service) => {
    setSelectedService(service)
    setRequestStatus('searching')
    
    // Simulate finding a worker (Uber-like)
    setTimeout(() => {
      const availableWorkers = mockWorkers.filter(w => w.profession === service.name && w.available)
      if (availableWorkers.length > 0) {
        setFoundWorker(availableWorkers[0])
        setRequestStatus('found')
        
        setTimeout(() => setRequestStatus('enroute'), 2000)
        setTimeout(() => setRequestStatus('arrived'), 5000)
      } else {
        setRequestStatus('idle')
        alert('No workers available for this service. Please try again later.')
      }
    }, 2000)
  }

  const cancelRequest = () => {
    setSelectedService(null)
    setRequestStatus('idle')
    setFoundWorker(null)
  }

  return (
    <div className="min-h-screen pt-16">
      <section className="relative min-h-[90vh] flex items-center bg-gradient-to-br from-violet-50 via-white to-cyan-50 overflow-hidden">
        <div className="absolute inset-0 overflow-hidden pointer-events-none">
          <motion.div
            animate={{ float: 6 }}
            className="absolute top-20 left-10 w-96 h-96 bg-gradient-to-br from-violet-400/20 to-fuchsia-400/20 rounded-full blur-3xl"
          />
          <motion.div
            animate={{ float: 8 }}
            className="absolute top-40 right-20 w-80 h-80 bg-gradient-to-br from-cyan-400/20 to-blue-400/20 rounded-full blur-3xl"
          />
          <motion.div
            animate={{ float: 10 }}
            className="absolute bottom-20 left-1/3 w-64 h-64 bg-gradient-to-br from-amber-400/20 to-orange-400/20 rounded-full blur-3xl"
          />
        </div>

        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 relative z-10">
          <div className="grid lg:grid-cols-2 gap-12 items-center">
            <motion.div
              initial={{ opacity: 0, x: -50 }}
              animate={{ opacity: 1, x: 0 }}
              transition={{ duration: 0.6 }}
            >
              <div className="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-violet-500 to-fuchsia-500 rounded-full text-white font-semibold text-sm mb-6 shadow-lg shadow-violet-500/30">
                <span className="w-2 h-2 bg-white rounded-full animate-pulse" />
                Trusted by 10,000+ customers in Kenya
              </div>
              
              <h1 className="font-display text-5xl sm:text-6xl lg:text-7xl font-bold text-gray-900 leading-tight mb-6">
                Find Trusted{' '}
                <span className="bg-gradient-to-r from-violet-600 via-fuchsia-500 to-cyan-500 bg-clip-text text-transparent">Professionals</span>{' '}
                Near You
              </h1>
              
              <p className="text-lg text-gray-600 mb-8 max-w-xl">
                Connect with verified experts across Kenya for all your home service needs. 
                From cleaning to electrical repairs, get the job done right.
              </p>
              
              <div className="flex flex-wrap gap-4">
                <Link to="/workers" className="px-8 py-4 bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white font-bold rounded-2xl shadow-xl shadow-violet-500/30 hover:shadow-2xl hover:shadow-violet-500/40 transition-all duration-300 hover:-translate-y-1 flex items-center gap-2">
                  Find a Worker
                  <ArrowRight className="w-5 h-5" />
                </Link>
                <Link to="/login" className="px-8 py-4 bg-white text-violet-600 font-bold rounded-2xl border-2 border-violet-200 hover:border-violet-400 hover:bg-violet-50 transition-all duration-300 hover:-translate-y-1 flex items-center gap-2">
                  Become a Worker
                </Link>
              </div>
            </motion.div>

            <motion.div
              initial={{ opacity: 0, x: 50 }}
              animate={{ opacity: 1, x: 0 }}
              transition={{ duration: 0.6, delay: 0.2 }}
              className="relative"
            >
              <div className="relative z-10 glass-card p-8 animate-float">
                <div className="w-full h-80 bg-gradient-to-br from-violet-100 via-fuchsia-50 to-cyan-100 rounded-2xl flex items-center justify-center">
                  <div className="text-center">
                    <div className="w-24 h-24 mx-auto mb-4 bg-gradient-to-br from-violet-500 to-fuchsia-500 rounded-2xl flex items-center justify-center shadow-lg shadow-violet-500/40">
                      <Wrench className="w-12 h-12 text-white" />
                    </div>
                    <p className="text-gray-700 font-semibold">Professional Services</p>
                  </div>
                </div>
                <motion.div
                  initial={{ scale: 0 }}
                  animate={{ scale: 1 }}
                  transition={{ delay: 0.5, type: 'spring' }}
                  className="absolute -bottom-6 -right-6 glass-card px-6 py-4 flex items-center gap-3"
                >
                  <div className="w-12 h-12 bg-gradient-to-r from-cyan-500 to-blue-500 rounded-full flex items-center justify-center shadow-lg shadow-cyan-500/30">
                    <CheckCircle className="w-6 h-6 text-white" />
                  </div>
                  <div>
                    <div className="font-semibold text-gray-900">Job Completed!</div>
                    <div className="text-sm text-gray-500">Just now</div>
                  </div>
                </motion.div>
              </div>
              
              <motion.div
                initial={{ scale: 0 }}
                animate={{ scale: 1 }}
                transition={{ delay: 0.7 }}
                className="absolute top-10 -left-8 glass-card px-4 py-3"
              >
                <div className="flex items-center gap-2">
                  <Star className="w-5 h-5 text-amber-400 fill-amber-400" />
                  <span className="font-bold text-gray-900">4.9 Rating</span>
                </div>
              </motion.div>
            </motion.div>
          </div>
        </div>
      </section>

      <section className="py-20 bg-white/50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            className="text-center mb-12"
          >
            <h2 className="font-display text-4xl sm:text-5xl font-bold text-gray-900 mb-4">
              Our Services
            </h2>
            <p className="text-gray-600 text-lg max-w-2xl mx-auto">
              Find the right professional for any task around your home
            </p>
          </motion.div>

          {/* Uber-like Quick Request Panel */}
          <div className="mt-12">
            {requestStatus === 'idle' ? (
              <motion.div 
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                className="glass-card p-8"
              >
                <h3 className="font-display text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                  <span className="text-3xl">🔥</span> Quick Book - Get Help Now
                </h3>
                <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                  {services.filter(s => s.id !== 'handyman').map((service) => (
                    <button
                      key={service.id}
                      onClick={() => handleRequestService(service)}
                      className={`p-6 rounded-2xl border-2 border-gray-100 hover:border-violet-400 hover:bg-violet-50 transition-all duration-300 flex flex-col items-center gap-3 group`}
                    >
                      <span className="text-3xl transform group-hover:scale-110 transition-transform">{service.icon}</span>
                      <span className="font-bold text-gray-700">{service.name}</span>
                    </button>
                  ))}
                </div>
              </motion.div>
            ) : (
              <motion.div 
                initial={{ opacity: 0, scale: 0.9 }}
                animate={{ opacity: 1, scale: 1 }}
                className="glass-card p-6"
              >
                {requestStatus === 'searching' && (
                  <div className="text-center py-10">
                    <div className="w-20 h-20 mx-auto mb-6 border-4 border-violet-500 border-t-transparent rounded-full animate-spin" />
                    <h3 className="font-display text-2xl font-bold text-gray-900 mb-3">
                      Finding {selectedService?.name}...
                    </h3>
                    <p className="text-gray-500 text-lg">We're connecting you with a nearby professional</p>
                    <button onClick={cancelRequest} className="mt-6 px-6 py-3 bg-red-100 text-red-600 font-semibold rounded-xl hover:bg-red-200 transition-colors">
                      Cancel
                    </button>
                  </div>
                )}
                
                {requestStatus === 'found' && foundWorker && (
                  <div className="text-center py-6">
                    <div className="w-20 h-20 mx-auto mb-6 bg-gradient-to-r from-cyan-500 to-blue-500 rounded-full flex items-center justify-center shadow-lg shadow-cyan-500/30">
                      <CheckCircle className="w-10 h-10 text-white" />
                    </div>
                    <h3 className="font-display text-2xl font-bold text-gray-900 mb-4">
                      Worker Found!
                    </h3>
                    <div className="flex items-center justify-center gap-6 mb-6">
                      <Avatar name={foundWorker.name} size="xl" />
                      <div className="text-left">
                        <p className="font-bold text-gray-900 text-lg">{foundWorker.name}</p>
                        <p className="text-gray-500">{foundWorker.profession} • {foundWorker.rating}★</p>
                        <p className="text-gray-500">{foundWorker.distance} away</p>
                      </div>
                    </div>
                    <p className="text-gray-500">Worker is on their way...</p>
                  </div>
                )}
                
                {requestStatus === 'enroute' && foundWorker && (
                  <div className="text-center py-6">
                    <div className="relative w-24 h-24 mx-auto mb-6">
                      <div className="absolute inset-0 border-4 border-cyan-500 rounded-full animate-ping opacity-30" />
                      <div className="w-24 h-24 bg-gradient-to-r from-cyan-500 to-blue-500 rounded-full flex items-center justify-center shadow-lg shadow-cyan-500/30">
                        <MapPin className="w-10 h-10 text-white" />
                      </div>
                    </div>
                    <h3 className="font-display text-2xl font-bold text-gray-900 mb-3">
                      {foundWorker.name} is on the way
                    </h3>
                    <p className="text-gray-500 text-lg">Arriving in about {foundWorker.distance}</p>
                  </div>
                )}
                
                {requestStatus === 'arrived' && foundWorker && (
                  <div className="text-center py-4">
                    <div className="w-16 h-16 mx-auto mb-4 bg-primary-500 rounded-full flex items-center justify-center">
                      <CheckCircle className="w-8 h-8 text-white" />
                    </div>
                    <h3 className="font-display text-xl font-semibold text-gray-900 mb-2">
                      Worker Arrived!
                    </h3>
                    <p className="text-gray-600 mb-4">{foundWorker.name} is at your location</p>
                    <div className="flex gap-3 justify-center">
                      <button 
                        onClick={() => {
                          alert('Payment initiated via ' + (paymentMethod === 'cod' ? 'Cash on Delivery' : 'M-Pesa'))
                          cancelRequest()
                        }}
                        className="btn-primary"
                      >
                        Pay Now
                      </button>
                      <button onClick={cancelRequest} className="btn-secondary">
                        Done
                      </button>
                    </div>
                  </div>
                )}
              </motion.div>
            )}
          </div>

          <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6 animate-stagger">
            {services.map((service, index) => (
              <motion.div
                key={service.id}
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ delay: index * 0.1 }}
                whileHover={{ scale: 1.05 }}
                className="service-card"
              >
                <Link to={`/workers?service=${service.id}`}>
                  <div className={`service-icon bg-gradient-to-br ${service.color}`}>
                    <span className="text-3xl">{service.icon}</span>
                  </div>
                  <h3 className="font-semibold text-gray-900 mb-1">{service.name}</h3>
                  <p className="text-sm text-gray-500">{service.description}</p>
                </Link>
              </motion.div>
            ))}
          </div>
        </div>
      </section>

      <section className="py-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            className="text-center mb-12"
          >
            <h2 className="font-display text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
              How It Works
            </h2>
          </motion.div>

          <div className="grid md:grid-cols-3 gap-8">
            {[
              { step: '01', title: 'Search', description: 'Browse through our network of verified professionals', icon: Search },
              { step: '02', title: 'Book', description: 'Choose a time slot that works best for you', icon: Calendar },
              { step: '03', title: 'Relax', description: 'Sit back while our expert gets the job done', icon: CheckCircle },
            ].map((item, index) => (
              <motion.div
                key={item.step}
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ delay: index * 0.2 }}
                className="glass-card p-8 text-center relative"
              >
                <div className="absolute -top-4 left-1/2 -translate-x-1/2 w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-600 rounded-full flex items-center justify-center text-white font-bold shadow-lg shadow-primary-500/30">
                  {item.step}
                </div>
                <div className="w-16 h-16 mx-auto mb-6 bg-primary-100 rounded-2xl flex items-center justify-center mt-4">
                  <item.icon className="w-8 h-8 text-primary-600" />
                </div>
                <h3 className="font-display text-xl font-semibold text-gray-900 mb-2">{item.title}</h3>
                <p className="text-gray-600">{item.description}</p>
              </motion.div>
            ))}
          </div>
        </div>
      </section>

      <section className="py-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <motion.div
            initial={{ opacity: 0, scale: 0.95 }}
            whileInView={{ opacity: 1, scale: 1 }}
            viewport={{ once: true }}
            className="glass-card p-12 text-center relative overflow-hidden"
            style={{
              background: 'linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(168, 85, 247, 0.1) 100%)'
            }}
          >
            <div className="absolute inset-0 bg-mesh opacity-50" />
            <div className="relative z-10">
              <h2 className="font-display text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                Ready to get started?
              </h2>
              <p className="text-gray-600 mb-8 max-w-2xl mx-auto">
                Join thousands of satisfied customers across Kenya who found the perfect professional
              </p>
              <div className="flex flex-wrap justify-center gap-4">
                <Link to="/workers" className="btn-primary">
                  Find a Worker Now
                </Link>
                <Link to="/login" className="btn-secondary">
                  Become a Pro
                </Link>
              </div>
            </div>
          </motion.div>
        </div>
      </section>
    </div>
  )
}

// Workers Discovery Page
function WorkersPage() {
  const [searchTerm, setSearchTerm] = useState('')
  const [selectedService, setSelectedService] = useState('all')
  const [selectedRating, setSelectedRating] = useState(0)
  const [selectedWorker, setSelectedWorker] = useState(null)

  const filteredWorkers = mockWorkers.filter(worker => {
    const matchesSearch = worker.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         worker.profession.toLowerCase().includes(searchTerm.toLowerCase())
    const matchesService = selectedService === 'all' || 
                          worker.profession.toLowerCase() === selectedService
    const matchesRating = worker.rating >= selectedRating
    return matchesSearch && matchesService && matchesRating
  })

  return (
    <div className="min-h-screen pt-20 pb-12 bg-mesh">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          className="mb-8"
        >
          <h1 className="font-display text-3xl sm:text-4xl font-bold text-gray-900 mb-2">
            Find Professionals
          </h1>
          <p className="text-gray-600">Browse and connect with verified service providers in Kenya</p>
        </motion.div>

        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.1 }}
          className="glass-card p-6 mb-8"
        >
          <div className="grid md:grid-cols-4 gap-4">
            <div className="md:col-span-2">
              <label className="block text-sm font-medium text-gray-700 mb-2">Search</label>
              <div className="relative">
                <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                <input
                  type="text"
                  placeholder="Search by name or profession..."
                  value={searchTerm}
                  onChange={(e) => setSearchTerm(e.target.value)}
                  className="input-field pl-10"
                />
              </div>
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Service</label>
              <select
                value={selectedService}
                onChange={(e) => setSelectedService(e.target.value)}
                className="input-field"
              >
                <option value="all">All Services</option>
                {services.map(s => (
                  <option key={s.id} value={s.name.toLowerCase()}>{s.name}</option>
                ))}
              </select>
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Min Rating</label>
              <select
                value={selectedRating}
                onChange={(e) => setSelectedRating(Number(e.target.value))}
                className="input-field"
              >
                <option value={0}>Any Rating</option>
                <option value={4}>4+ Stars</option>
                <option value={4.5}>4.5+ Stars</option>
              </select>
            </div>
          </div>
        </motion.div>

        {/* Map Section */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.2 }}
          className="glass-card p-6 mb-8"
        >
          <div className="flex items-center justify-between mb-4">
            <div className="flex items-center gap-2">
              <Map className="w-5 h-5 text-primary-600" />
              <h2 className="font-semibold text-gray-900">Live Worker Map</h2>
              <span className="text-sm text-gray-500">({filteredWorkers.length} available)</span>
            </div>
          </div>
          <div className="h-80 rounded-xl overflow-hidden shadow-lg">
            <MapContainer 
              center={[-1.2921, 36.8219]} 
              zoom={6} 
              style={{ height: '100%', width: '100%' }}
              className="z-0"
            >
              <TileLayer
                attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
              />
              {filteredWorkers.map((worker) => (
                <Marker 
                  key={worker.id} 
                  position={[worker.lat, worker.lng]}
                  icon={worker.available ? availableIcon : busyIcon}
                >
                  <Popup>
                    <div className="text-center min-w-40">
                      <div className="font-semibold text-gray-900">{worker.name}</div>
                      <div className="text-primary-600 text-sm">{worker.profession}</div>
                      <div className="flex items-center justify-center gap-1 mt-1">
                        <Star className="w-3 h-3 text-yellow-400 fill-yellow-400" />
                        <span className="text-sm text-gray-600">{worker.rating} ({worker.reviews})</span>
                      </div>
                      <div className="text-xs text-gray-500 mt-1">{worker.location}</div>
                      <div className="font-bold text-accent-600 mt-2">{formatCurrency(worker.price)}/hr</div>
                      <div className={`mt-2 px-2 py-1 rounded-full text-xs text-white ${worker.available ? 'bg-accent-500' : 'bg-gray-400'}`}>
                        {worker.available ? 'Available' : 'Busy'}
                      </div>
                    </div>
                  </Popup>
                </Marker>
              ))}
            </MapContainer>
          </div>
        </motion.div>

        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
          {filteredWorkers.map((worker, index) => (
            <motion.div
              key={worker.id}
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: index * 0.1 }}
              whileHover={{ y: -5 }}
              className="glass-card overflow-hidden group"
            >
              <Link to={`/worker/${worker.id}`}>
                <div className="relative h-48 overflow-hidden bg-gradient-to-br from-primary-100 to-purple-100 flex items-center justify-center">
                  <Avatar name={worker.name} size="xl" className="w-24 h-24" />
                  <div className="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent" />
                  <div className="absolute bottom-4 left-4 right-4">
                    <div className="flex items-center gap-2 text-white">
                      <StarRating rating={Math.floor(worker.rating)} size="sm" />
                      <span className="text-sm">({worker.reviews})</span>
                    </div>
                  </div>
                  {!worker.available && (
                    <div className="absolute top-4 right-4 px-3 py-1 bg-red-500 text-white text-sm rounded-full">
                      Busy
                    </div>
                  )}
                  <div className="absolute top-4 left-4 px-3 py-1 bg-white/90 text-gray-700 text-sm rounded-full flex items-center gap-1">
                    <MapPin className="w-3 h-3" />
                    {worker.location}
                  </div>
                </div>
                <div className="p-5">
                  <div className="flex items-start justify-between mb-2">
                    <div>
                      <h3 className="font-semibold text-gray-900">{worker.name}</h3>
                      <p className="text-primary-600 text-sm">{worker.profession}</p>
                    </div>
                    <div className="text-right">
                      <div className="font-bold text-gray-900">{formatCurrency(worker.price)}</div>
                      <div className="text-xs text-gray-500">/hour</div>
                    </div>
                  </div>
                  <div className="flex items-center gap-2 text-sm text-gray-500 mb-4">
                    <MapPin className="w-4 h-4" />
                    {worker.distance} away
                  </div>
                  <div className="flex gap-2">
                    <Link
                      to={`/worker/${worker.id}`}
                      className="flex-1 btn-primary text-center text-sm py-2"
                    >
                      View Profile
                    </Link>
                    <Link
                      to={`/booking/${worker.id}`}
                      className="flex-1 btn-accent text-center text-sm py-2"
                    >
                      Book Now
                    </Link>
                  </div>
                </div>
              </Link>
            </motion.div>
          ))}
        </div>

        {filteredWorkers.length === 0 && (
          <div className="text-center py-12">
            <Search className="w-16 h-16 text-gray-300 mx-auto mb-4" />
            <h3 className="text-xl font-semibold text-gray-900 mb-2">No workers found</h3>
            <p className="text-gray-500">Try adjusting your search or filters</p>
          </div>
        )}
      </div>
    </div>
  )
}

// Worker Profile Page
function WorkerProfilePage() {
  const worker = mockWorkers[0]

  return (
    <div className="min-h-screen pt-20 pb-12 bg-mesh">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div className="grid lg:grid-cols-3 gap-8">
          <motion.div
            initial={{ opacity: 0, x: -20 }}
            animate={{ opacity: 1, x: 0 }}
            className="lg:col-span-1"
          >
            <div className="glass-card p-6 sticky top-24">
              <div className="text-center mb-6">
                <div className="relative inline-block">
                  <Avatar name={worker.name} size="xl" className="w-32 h-32 mx-auto mb-4" />
                  {worker.available && (
                    <div className="absolute bottom-2 right-2 w-5 h-5 bg-accent-500 border-4 border-white rounded-full" />
                  )}
                </div>
                <h1 className="font-display text-2xl font-bold text-gray-900">{worker.name}</h1>
                <p className="text-primary-600 font-medium">{worker.profession}</p>
              </div>

              <div className="flex items-center justify-center gap-2 mb-6">
                <StarRating rating={Math.floor(worker.rating)} />
                <span className="text-gray-600">({worker.reviews} reviews)</span>
              </div>

              <div className="space-y-3 mb-6">
                <div className="flex items-center gap-3 text-gray-600">
                  <MapPin className="w-5 h-5 text-gray-400" />
                  <span>{worker.location}</span>
                </div>
                <div className="flex items-center gap-3 text-gray-600">
                  <MapPin className="w-5 h-5 text-gray-400" />
                  <span>{worker.distance} away</span>
                </div>
                <div className="flex items-center gap-3 text-gray-600">
                  <Clock className="w-5 h-5 text-gray-400" />
                  <span>{worker.available ? 'Available now' : 'Currently busy'}</span>
                </div>
                <div className="flex items-center gap-3 text-gray-600">
                  <Briefcase className="w-5 h-5 text-gray-400" />
                  <span>150+ jobs completed</span>
                </div>
                <div className="flex items-center gap-3 text-gray-600">
                  <User className="w-5 h-5 text-gray-400" />
                  <span>{worker.phone}</span>
                </div>
              </div>

              <div className="text-center mb-6">
                <div className="text-3xl font-bold text-gray-900">{formatCurrency(worker.price)}</div>
                <div className="text-gray-500">per hour</div>
              </div>

              <div className="space-y-3">
                <Link to={`/booking/${worker.id}`} className="btn-primary w-full text-center block">
                  Book Now
                </Link>
                <button className="btn-secondary w-full">
                  Contact
                </button>
              </div>
            </div>
          </motion.div>

          <motion.div
            initial={{ opacity: 0, x: 20 }}
            animate={{ opacity: 1, x: 0 }}
            transition={{ delay: 0.1 }}
            className="lg:col-span-2 space-y-6"
          >
            <div className="glass-card p-6">
              <h2 className="font-display text-xl font-semibold text-gray-900 mb-4">About</h2>
              <p className="text-gray-600 leading-relaxed">
                Experienced {worker.profession.toLowerCase()} with over 10 years of expertise in Kenya. 
                I pride myself on delivering high-quality work and ensuring customer satisfaction. 
                Based in {worker.location}, I serve all major towns in Kenya. Always punctual and professional.
              </p>
            </div>

            <div className="glass-card p-6">
              <h2 className="font-display text-xl font-semibold text-gray-900 mb-4">Services Offered</h2>
              <div className="grid sm:grid-cols-2 gap-3">
                {['Electrical installations', 'Wiring & rewiring', 'Circuit repairs', 'Safety inspections'].map((service, i) => (
                  <div key={i} className="flex items-center gap-2 text-gray-600">
                    <CheckCircle className="w-5 h-5 text-accent-500" />
                    {service}
                  </div>
                ))}
              </div>
            </div>

            <div className="glass-card p-6">
              <h2 className="font-display text-xl font-semibold text-gray-900 mb-4">Reviews</h2>
              <div className="space-y-4">
                {mockReviews.map((review) => (
                  <div key={review.id} className="p-4 bg-gray-50/50 rounded-xl">
                    <div className="flex items-start gap-3">
                      <Avatar name={review.user} size="md" />
                      <div className="flex-1">
                        <div className="flex items-center justify-between mb-1">
                          <h4 className="font-semibold text-gray-900">{review.user}</h4>
                          <span className="text-sm text-gray-500">{review.date}</span>
                        </div>
                        <StarRating rating={review.rating} size="sm" />
                        <p className="text-gray-600 mt-2">{review.comment}</p>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>

            <div className="glass-card p-6">
              <h2 className="font-display text-xl font-semibold text-gray-900 mb-4">Service Areas</h2>
              <div className="flex flex-wrap gap-2">
                {['Nairobi', 'Mombasa', 'Kisumu', 'Nakuru', 'Eldoret', 'Kakamega'].map((area, i) => (
                  <span key={i} className="px-4 py-2 bg-primary-100 text-primary-700 rounded-full text-sm">
                    {area}
                  </span>
                ))}
              </div>
            </div>
          </motion.div>
        </div>
      </div>
    </div>
  )
}

// Booking Page
function BookingPage() {
  const [step, setStep] = useState(1)
  const [selectedDate, setSelectedDate] = useState('')
  const [selectedTime, setSelectedTime] = useState('')
  const [paymentMethod, setPaymentMethod] = useState('mpesa')

  const worker = mockWorkers[0]

  const timeSlots = ['9:00 AM', '10:00 AM', '11:00 AM', '1:00 PM', '2:00 PM', '3:00 PM', '4:00 PM']

  const handleBooking = () => {
    if (paymentMethod === 'cod') {
      alert('Booking confirmed! You will pay with Cash on Delivery.')
    } else {
      alert('Redirecting to M-Pesa payment...')
    }
  }

  return (
    <div className="min-h-screen pt-20 pb-12 bg-mesh">
      <div className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
        >
          <h1 className="font-display text-3xl font-bold text-gray-900 mb-8">Book a Service</h1>

          <div className="flex items-center justify-center gap-4 mb-8">
            {[1, 2, 3].map((s) => (
              <div key={s} className="flex items-center">
                <div className={`w-10 h-10 rounded-full flex items-center justify-center font-semibold transition-colors ${
                  step >= s ? 'bg-primary-500 text-white' : 'bg-gray-200 text-gray-500'
                }`}>
                  {s}
                </div>
                {s < 3 && (
                  <div className={`w-16 h-1 mx-2 rounded ${step > s ? 'bg-primary-500' : 'bg-gray-200'}`} />
                )}
              </div>
            ))}
          </div>

          <div className="glass-card p-6">
            {step === 1 && (
              <motion.div
                initial={{ opacity: 0, x: 20 }}
                animate={{ opacity: 1, x: 0 }}
              >
                <h2 className="font-display text-xl font-semibold text-gray-900 mb-6">Service Details</h2>
                
                <div className="flex items-center gap-4 p-4 bg-gray-50/50 rounded-xl mb-6">
                  <Avatar name={worker.name} size="lg" className="w-16 h-16" />
                  <div>
                    <h3 className="font-semibold text-gray-900">{worker.name}</h3>
                    <p className="text-primary-600">{worker.profession}</p>
                  </div>
                </div>

                <div className="space-y-4 mb-6">
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">Service Type</label>
                    <select className="input-field">
                      <option>Electrical Repair</option>
                      <option>Installation</option>
                      <option>Inspection</option>
                    </select>
                  </div>
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea
                      className="input-field h-24 resize-none"
                      placeholder="Describe what you need help with..."
                    />
                  </div>
                </div>

                <button onClick={() => setStep(2)} className="btn-primary w-full">
                  Continue
                </button>
              </motion.div>
            )}

            {step === 2 && (
              <motion.div
                initial={{ opacity: 0, x: 20 }}
                animate={{ opacity: 1, x: 0 }}
              >
                <h2 className="font-display text-xl font-semibold text-gray-900 mb-6">Select Date & Time</h2>

                <div className="space-y-4 mb-6">
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">Date</label>
                    <input
                      type="date"
                      value={selectedDate}
                      onChange={(e) => setSelectedDate(e.target.value)}
                      className="input-field"
                    />
                  </div>
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">Time Slot</label>
                    <div className="grid grid-cols-3 sm:grid-cols-4 gap-2">
                      {timeSlots.map((time) => (
                        <button
                          key={time}
                          onClick={() => setSelectedTime(time)}
                          className={`p-3 rounded-xl text-sm font-medium transition-all ${
                            selectedTime === time
                              ? 'bg-primary-500 text-white'
                              : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                          }`}
                        >
                          {time}
                        </button>
                      ))}
                    </div>
                  </div>
                </div>

                <div className="flex gap-3">
                  <button onClick={() => setStep(1)} className="btn-secondary flex-1">
                    Back
                  </button>
                  <button onClick={() => setStep(3)} className="btn-primary flex-1">
                    Continue
                  </button>
                </div>
              </motion.div>
            )}

            {step === 3 && (
              <motion.div
                initial={{ opacity: 0, x: 20 }}
                animate={{ opacity: 1, x: 0 }}
              >
                <h2 className="font-display text-xl font-semibold text-gray-900 mb-6">Confirm Booking</h2>

                <div className="space-y-4 mb-6">
                  <div className="p-4 bg-gray-50/50 rounded-xl">
                    <h4 className="font-semibold text-gray-900 mb-3">Booking Summary</h4>
                    <div className="space-y-2 text-sm">
                      <div className="flex justify-between">
                        <span className="text-gray-500">Service</span>
                        <span className="text-gray-900">Electrical Repair</span>
                      </div>
                      <div className="flex justify-between">
                        <span className="text-gray-500">Professional</span>
                        <span className="text-gray-900">{worker.name}</span>
                      </div>
                      <div className="flex justify-between">
                        <span className="text-gray-500">Date</span>
                        <span className="text-gray-900">{selectedDate || 'Not selected'}</span>
                      </div>
                      <div className="flex justify-between">
                        <span className="text-gray-500">Time</span>
                        <span className="text-gray-900">{selectedTime || 'Not selected'}</span>
                      </div>
                    </div>
                  </div>

                  <div className="p-4 bg-primary-50/50 rounded-xl">
                    <div className="flex justify-between items-center">
                      <span className="font-semibold text-gray-900">Estimated Total</span>
                      <span className="text-2xl font-bold text-primary-600">{formatCurrency(worker.price * 2)}</span>
                    </div>
                    <p className="text-sm text-gray-500 mt-1">Based on 2 hour minimum</p>
                  </div>

                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                    <div className="grid grid-cols-2 gap-3">
                      <button 
                        type="button"
                        onClick={() => setPaymentMethod('mpesa')}
                        className={`p-4 border-2 rounded-xl text-center transition-all ${
                          paymentMethod === 'mpesa' ? 'border-primary-500 bg-primary-50' : 'border-gray-200 hover:border-gray-300'
                        }`}
                      >
                        <div className="font-medium text-gray-900">M-Pesa</div>
                      </button>
                      <button 
                        type="button"
                        onClick={() => setPaymentMethod('cod')}
                        className={`p-4 border-2 rounded-xl text-center transition-all ${
                          paymentMethod === 'cod' ? 'border-primary-500 bg-primary-50' : 'border-gray-200 hover:border-gray-300'
                        }`}
                      >
                        <div className="font-medium text-gray-600">Cash on Delivery</div>
                      </button>
                    </div>
                  </div>
                </div>

                <div className="flex gap-3">
                  <button onClick={() => setStep(2)} className="btn-secondary flex-1">
                    Back
                  </button>
                  <button onClick={handleBooking} className="btn-accent flex-1">
                    Confirm Booking
                  </button>
                </div>
              </motion.div>
            )}
          </div>
        </motion.div>
      </div>
    </div>
  )
}

// Customer Dashboard
function CustomerDashboard() {
  const { user } = useAuth()
  
  const stats = [
    { label: 'Total Bookings', value: '12', icon: Calendar, color: 'from-blue-400 to-blue-600' },
    { label: 'Saved Workers', value: '5', icon: User, color: 'from-purple-400 to-purple-600' },
    { label: 'Active Jobs', value: '2', icon: Briefcase, color: 'from-orange-400 to-orange-600' },
    { label: 'Total Spent', value: formatCurrency(45000), icon: BarChart3, color: 'from-accent-400 to-accent-600' },
  ]

  const recentBookings = [
    { id: 1, worker: 'John Kiprotich', service: 'Electrical Repair', status: 'completed', date: 'Yesterday', price: 10000 },
    { id: 2, worker: 'Sarah Wanjiku', service: 'Deep Cleaning', status: 'scheduled', date: 'Tomorrow', price: 7000 },
    { id: 3, worker: 'Michael Ochieng', service: 'Plumbing Fix', status: 'pending', date: 'Mar 15', price: 11000 },
  ]

  return (
    <div className="min-h-screen pt-20 pb-12 bg-mesh">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
        >
          <div className="flex items-center justify-between mb-8">
            <div>
              <h1 className="font-display text-3xl font-bold text-gray-900">Dashboard</h1>
              <p className="text-gray-600">Welcome back, {user?.name || 'User'}!</p>
            </div>
            <Link to="/workers" className="btn-primary flex items-center gap-2">
              <Calendar className="w-5 h-5" />
              New Booking
            </Link>
          </div>

          <div className="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            {stats.map((stat, index) => (
              <motion.div
                key={stat.label}
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ delay: index * 0.1 }}
                className="glass-card p-5"
              >
                <div className={`w-12 h-12 bg-gradient-to-br ${stat.color} rounded-xl flex items-center justify-center mb-4`}>
                  <stat.icon className="w-6 h-6 text-white" />
                </div>
                <div className="text-2xl font-bold text-gray-900">{stat.value}</div>
                <div className="text-sm text-gray-500">{stat.label}</div>
              </motion.div>
            ))}
          </div>

          <div className="grid lg:grid-cols-3 gap-8">
            <div className="lg:col-span-2 glass-card p-6">
              <div className="flex items-center justify-between mb-6">
                <h2 className="font-display text-xl font-semibold text-gray-900">Recent Bookings</h2>
                <button className="text-primary-600 font-medium text-sm hover:underline">View All</button>
              </div>
              <div className="space-y-4">
                {recentBookings.map((booking) => (
                  <div key={booking.id} className="p-4 bg-gray-50/50 rounded-xl flex items-center gap-4">
                    <div className="flex-1">
                      <h4 className="font-semibold text-gray-900">{booking.worker}</h4>
                      <p className="text-sm text-gray-500">{booking.service}</p>
                    </div>
                    <div className="text-right">
                      <span className={`badge ${
                        booking.status === 'completed' ? 'bg-accent-100 text-accent-700' :
                        booking.status === 'scheduled' ? 'bg-blue-100 text-blue-700' :
                        'bg-yellow-100 text-yellow-700'
                      }`}>
                        {booking.status}
                      </span>
                      <p className="text-sm text-gray-500 mt-1">{booking.date}</p>
                    </div>
                    <div className="text-right">
                      <div className="font-semibold text-gray-900">{formatCurrency(booking.price)}</div>
                    </div>
                  </div>
                ))}
              </div>
            </div>

            <div className="space-y-6">
              <div className="glass-card p-6">
                <h2 className="font-display text-xl font-semibold text-gray-900 mb-4">Quick Actions</h2>
                <div className="space-y-3">
                  <Link to="/workers" className="flex items-center gap-3 p-3 bg-gray-50/50 rounded-xl hover:bg-gray-100 transition-colors">
                    <Search className="w-5 h-5 text-primary-600" />
                    <span className="text-gray-700">Find Workers</span>
                  </Link>
                  <button items-center gap- className="flex3 p-3 bg-gray-50/50 rounded-xl hover:bg-gray-100 transition-colors w-full">
                    <User className="w-5 h-5 text-purple-600" />
                    <span className="text-gray-700">Saved Workers</span>
                  </button>
                  <button className="flex items-center gap-3 p-3 bg-gray-50/50 rounded-xl hover:bg-gray-100 transition-colors w-full">
                    <Bell className="w-5 h-5 text-orange-600" />
                    <span className="text-gray-700">Notifications</span>
                  </button>
                </div>
              </div>

              <div className="glass-card p-6">
                <h2 className="font-display text-xl font-semibold text-gray-900 mb-4">Notifications</h2>
                <div className="space-y-3">
                  <div className="p-3 bg-blue-50/50 rounded-xl">
                    <p className="text-sm text-gray-700">Sarah Wanjiku confirmed your booking for tomorrow</p>
                    <p className="text-xs text-gray-500 mt-1">2 hours ago</p>
                  </div>
                  <div className="p-3 bg-accent-50/50 rounded-xl">
                    <p className="text-sm text-gray-700">John Kiprotich completed the job!</p>
                    <p className="text-xs text-gray-500 mt-1">1 day ago</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </motion.div>
      </div>
    </div>
  )
}

// Worker Dashboard
function WorkerDashboard() {
  const { user } = useAuth()
  const [isOnline, setIsOnline] = useState(true)
  
  const stats = [
    { label: 'This Week', value: formatCurrency(34000), icon: DollarSign, color: 'from-accent-400 to-accent-600' },
    { label: 'Pending Jobs', value: '3', icon: Clock, color: 'from-orange-400 to-orange-600' },
    { label: 'Completed', value: '28', icon: CheckCircle, color: 'from-blue-400 to-blue-600' },
    { label: 'Rating', value: '4.9★', icon: Star, color: 'from-yellow-400 to-yellow-600' },
  ]

  return (
    <div className="min-h-screen pt-20 pb-12 bg-mesh">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
        >
          <div className="flex items-center justify-between mb-8">
            <div>
              <h1 className="font-display text-3xl font-bold text-gray-900">Worker Dashboard</h1>
              <p className="text-gray-600">Welcome back, {user?.name || 'Worker'}!</p>
            </div>
            <div className="flex items-center gap-3">
              <button
                onClick={() => setIsOnline(!isOnline)}
                className={`glass-card px-4 py-2 flex items-center gap-2 cursor-pointer ${
                  isOnline ? 'border-accent-500 bg-accent-50' : 'border-red-500 bg-red-50'
                }`}
              >
                <div className={`w-3 h-3 rounded-full ${isOnline ? 'bg-accent-500' : 'bg-red-500'} ${isOnline && 'animate-pulse'}`} />
                <span className={`font-medium ${isOnline ? 'text-accent-700' : 'text-red-700'}`}>
                  {isOnline ? 'Online' : 'Offline'}
                </span>
              </button>
            </div>
          </div>

          <div className="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            {stats.map((stat, index) => (
              <motion.div
                key={stat.label}
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ delay: index * 0.1 }}
                className="glass-card p-5"
              >
                <div className={`w-12 h-12 bg-gradient-to-br ${stat.color} rounded-xl flex items-center justify-center mb-4`}>
                  <stat.icon className="w-6 h-6 text-white" />
                </div>
                <div className="text-2xl font-bold text-gray-900">{stat.value}</div>
                <div className="text-sm text-gray-500">{stat.label}</div>
              </motion.div>
            ))}
          </div>

          <div className="grid lg:grid-cols-3 gap-8">
            <div className="lg:col-span-2 glass-card p-6">
              <h2 className="font-display text-xl font-semibold text-gray-900 mb-6">Incoming Requests</h2>
              <div className="space-y-4">
                {[
                  { id: 1, user: 'Alex Kamau', service: 'Electrical Repair', address: 'Nairobi, Kenya', distance: '0.8 km', price: 10000, time: 'Today, 2:00 PM', phone: '+254700000001' },
                  { id: 2, user: 'Maryanne Moraa', service: 'Wiring Installation', address: 'Kisumu, Kenya', distance: '2.1 km', price: 15000, time: 'Tomorrow, 10:00 AM', phone: '+254700000002' },
                  { id: 3, user: 'James Otieno', service: 'Safety Inspection', address: 'Mombasa, Kenya', distance: '1.5 km', price: 7500, time: 'Mar 15, 9:00 AM', phone: '+254700000003' },
                ].map((job) => (
                  <div key={job.id} className="p-4 bg-gray-50/50 rounded-xl">
                    <div className="flex items-start justify-between mb-3">
                      <div>
                        <h4 className="font-semibold text-gray-900">{job.user}</h4>
                        <p className="text-primary-600 text-sm">{job.service}</p>
                        <p className="text-xs text-gray-500 mt-1">{job.phone}</p>
                      </div>
                      <div className="text-right">
                        <div className="font-bold text-gray-900">{formatCurrency(job.price)}</div>
                        <p className="text-xs text-gray-500">{job.time}</p>
                      </div>
                    </div>
                    <div className="flex items-center justify-between">
                      <div className="flex items-center gap-2 text-sm text-gray-500">
                        <MapPin className="w-4 h-4" />
                        {job.address} • {job.distance}
                      </div>
                      <div className="flex gap-2">
                        <button className="px-4 py-2 bg-red-100 text-red-600 rounded-lg text-sm font-medium hover:bg-red-200 transition-colors">
                          Decline
                        </button>
                        <button className="px-4 py-2 bg-accent-500 text-white rounded-lg text-sm font-medium hover:bg-accent-600 transition-colors">
                          Accept
                        </button>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>

            <div className="glass-card p-6">
              <h2 className="font-display text-xl font-semibold text-gray-900 mb-4">Weekly Earnings</h2>
              <div className="h-48 flex items-end justify-between gap-2">
                {[65, 45, 80, 55, 90, 70, 85].map((height, i) => (
                  <div key={i} className="flex-1 flex flex-col items-center gap-2">
                    <motion.div
                      initial={{ height: 0 }}
                      animate={{ height: `${height}%` }}
                      transition={{ delay: i * 0.1, duration: 0.5 }}
                      className="w-full bg-gradient-to-t from-primary-500 to-primary-600 rounded-t-lg"
                    />
                    <span className="text-xs text-gray-500">{['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'][i]}</span>
                  </div>
                ))}
              </div>
              <div className="mt-4 pt-4 border-t border-gray-100">
                <div className="flex justify-between items-center">
                  <span className="text-gray-500">Total this week</span>
                  <span className="text-xl font-bold text-gray-900">{formatCurrency(34000)}</span>
                </div>
              </div>
            </div>
          </div>
        </motion.div>
      </div>
    </div>
  )
}

// Admin Dashboard
function AdminDashboard() {
  const [showAddAdmin, setShowAddAdmin] = useState(false)
  const [newAdmin, setNewAdmin] = useState({ name: '', email: '', role: 'admin' })
  
  const stats = [
    { label: 'Total Users', value: '12,450', icon: Users, color: 'from-blue-400 to-blue-600', change: '+12%' },
    { label: 'Active Workers', value: '5,234', icon: UserCheck, color: 'from-accent-400 to-accent-600', change: '+8%' },
    { label: 'Total Jobs', value: '45,678', icon: Briefcase, color: 'from-purple-400 to-purple-600', change: '+23%' },
    { label: 'Revenue', value: formatCurrency(23400000), icon: DollarSign, color: 'from-orange-400 to-orange-600', change: '+18%' },
  ]

  const handleExportReport = () => {
    const reportData = {
      generatedAt: new Date().toISOString(),
      totalUsers: 12450,
      activeWorkers: 5234,
      totalJobs: 45678,
      revenue: 23400000,
    }
    const blob = new Blob([JSON.stringify(reportData, null, 2)], { type: 'application/json' })
    const url = URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = `handsOn-report-${new Date().toISOString().split('T')[0]}.json`
    a.click()
  }

  const handleAddAdmin = (e) => {
    e.preventDefault()
    alert(`Admin ${newAdmin.name} would be added with email: ${newAdmin.email}`)
    setShowAddAdmin(false)
    setNewAdmin({ name: '', email: '', role: 'admin' })
  }

  return (
    <div className="min-h-screen pt-20 pb-12 bg-mesh">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
        >
          <div className="flex items-center justify-between mb-8">
            <div>
              <h1 className="font-display text-3xl font-bold text-gray-900">Admin Dashboard</h1>
              <p className="text-gray-600">Platform overview and management</p>
            </div>
            <div className="flex gap-3">
              <button 
                onClick={handleExportReport}
                className="btn-secondary flex items-center gap-2"
              >
                <Download className="w-5 h-5" />
                Export Report
              </button>
              <button 
                onClick={() => setShowAddAdmin(true)}
                className="btn-primary flex items-center gap-2"
              >
                <Plus className="w-5 h-5" />
                Add Admin
              </button>
            </div>
          </div>

          {/* Add Admin Modal */}
          <AnimatePresence>
            {showAddAdmin && (
              <motion.div
                initial={{ opacity: 0 }}
                animate={{ opacity: 1 }}
                exit={{ opacity: 0 }}
                className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
                onClick={() => setShowAddAdmin(false)}
              >
                <motion.div
                  initial={{ scale: 0.9 }}
                  animate={{ scale: 1 }}
                  exit={{ scale: 0.9 }}
                  className="glass-card p-6 w-full max-w-md"
                  onClick={e => e.stopPropagation()}
                >
                  <h2 className="font-display text-xl font-semibold text-gray-900 mb-4">Add New Admin</h2>
                  <form onSubmit={handleAddAdmin} className="space-y-4">
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                      <input
                        type="text"
                        value={newAdmin.name}
                        onChange={(e) => setNewAdmin({ ...newAdmin, name: e.target.value })}
                        className="input-field"
                        placeholder="Enter full name"
                        required
                      />
                    </div>
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-2">Email</label>
                      <input
                        type="email"
                        value={newAdmin.email}
                        onChange={(e) => setNewAdmin({ ...newAdmin, email: e.target.value })}
                        className="input-field"
                        placeholder="admin@example.com"
                        required
                      />
                    </div>
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-2">Role</label>
                      <select
                        value={newAdmin.role}
                        onChange={(e) => setNewAdmin({ ...newAdmin, role: e.target.value })}
                        className="input-field"
                      >
                        <option value="admin">Super Admin</option>
                        <option value="moderator">Moderator</option>
                        <option value="support">Support</option>
                      </select>
                    </div>
                    <div className="flex gap-3 pt-4">
                      <button
                        type="button"
                        onClick={() => setShowAddAdmin(false)}
                        className="btn-secondary flex-1"
                      >
                        Cancel
                      </button>
                      <button type="submit" className="btn-primary flex-1">
                        Add Admin
                      </button>
                    </div>
                  </form>
                </motion.div>
              </motion.div>
            )}
          </AnimatePresence>

          <div className="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            {stats.map((stat, index) => (
              <motion.div
                key={stat.label}
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ delay: index * 0.1 }}
                className="glass-card p-5"
              >
                <div className="flex items-center justify-between mb-4">
                  <div className={`w-12 h-12 bg-gradient-to-br ${stat.color} rounded-xl flex items-center justify-center`}>
                    <stat.icon className="w-6 h-6 text-white" />
                  </div>
                  <span className="text-sm font-medium text-accent-600 bg-accent-100 px-2 py-1 rounded-full">
                    {stat.change}
                  </span>
                </div>
                <div className="text-2xl font-bold text-gray-900">{stat.value}</div>
                <div className="text-sm text-gray-500">{stat.label}</div>
              </motion.div>
            ))}
          </div>

          <div className="grid lg:grid-cols-3 gap-8">
            <div className="lg:col-span-2 glass-card p-6">
              <h2 className="font-display text-xl font-semibold text-gray-900 mb-6">Revenue Overview</h2>
              <div className="h-64 flex items-end justify-between gap-4">
                {['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'].map((month, i) => (
                  <div key={i} className="flex-1 flex flex-col items-center gap-2">
                    <motion.div
                      initial={{ height: 0 }}
                      animate={{ height: `${[40, 55, 45, 70, 65, 85][i]}%` }}
                      transition={{ delay: i * 0.1, duration: 0.5 }}
                      className="w-full bg-gradient-to-t from-primary-500 to-primary-600 rounded-t-lg relative"
                    >
                      <div className="absolute -top-8 left-1/2 -translate-x-1/2 text-sm font-medium text-gray-700">
                        {formatCurrency([40, 55, 45, 70, 65, 85][i] * 100000).replace('.00', '')}
                      </div>
                    </motion.div>
                    <span className="text-sm text-gray-500">{month}</span>
                  </div>
                ))}
              </div>
            </div>

            <div className="glass-card p-6">
              <h2 className="font-display text-xl font-semibold text-gray-900 mb-4">Recent Worker Registrations</h2>
              <div className="space-y-3">
                {[
                  { name: 'Peter Kimani', service: 'Electrician', date: '2 hours ago' },
                  { name: 'Grace Atieno', service: 'Plumber', date: '5 hours ago' },
                  { name: 'Samuel Ochieng', service: 'Painter', date: '1 day ago' },
                  { name: 'Faith Wambui', service: 'Cleaner', date: '2 days ago' },
                ].map((worker, i) => (
                  <div key={i} className="flex items-center justify-between p-3 bg-gray-50/50 rounded-xl">
                    <div>
                      <h4 className="font-medium text-gray-900">{worker.name}</h4>
                      <p className="text-sm text-gray-500">{worker.service}</p>
                    </div>
                    <button className="text-primary-600 text-sm font-medium hover:underline">
                      Approve
                    </button>
                  </div>
                ))}
              </div>
            </div>

            <div className="glass-card p-6">
              <h2 className="font-display text-xl font-semibold text-gray-900 mb-4">Jobs by Category</h2>
              <div className="space-y-4">
                {[
                  { service: 'Cleaning', percentage: 35, color: 'bg-blue-500' },
                  { service: 'Electrical', percentage: 25, color: 'bg-yellow-500' },
                  { service: 'Plumbing', percentage: 20, color: 'bg-hot-500' },
                  { service: 'Painting', percentage: 12, color: 'bg-purple-500' },
                  { service: 'Other', percentage: 8, color: 'bg-gray-500' },
                ].map((item, i) => (
                  <div key={i}>
                    <div className="flex justify-between text-sm mb-1">
                      <span className="text-gray-700">{item.service}</span>
                      <span className="text-gray-500">{item.percentage}%</span>
                    </div>
                    <div className="h-2 bg-gray-100 rounded-full overflow-hidden">
                      <motion.div
                        initial={{ width: 0 }}
                        animate={{ width: `${item.percentage}%` }}
                        transition={{ delay: 0.3, duration: 0.8 }}
                        className={`h-full ${item.color} rounded-full`}
                      />
                    </div>
                  </div>
                ))}
              </div>
            </div>

            <div className="glass-card p-6">
              <h2 className="font-display text-xl font-semibold text-gray-900 mb-4">System Status</h2>
              <div className="space-y-4">
                {[
                  { label: 'API Server', status: 'Online', color: 'text-accent-600' },
                  { label: 'Database', status: 'Online', color: 'text-accent-600' },
                  { label: 'Payment Gateway (M-Pesa)', status: 'Online', color: 'text-accent-600' },
                  { label: 'Email Service', status: 'Online', color: 'text-accent-600' },
                ].map((item, i) => (
                  <div key={i} className="flex items-center justify-between p-3 bg-gray-50/50 rounded-xl">
                    <span className="text-gray-700">{item.label}</span>
                    <span className={`font-medium ${item.color}`}>{item.status}</span>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </motion.div>
      </div>
    </div>
  )
}

// Main App with Routing
function App() {
  return (
    <AuthProvider>
      <Router>
        <div className="min-h-screen">
          <Navbar />
          <AnimatePresence mode="wait">
            <Routes>
              <Route path="/" element={<HomePage />} />
              <Route path="/login" element={<LoginPage />} />
              <Route path="/register" element={<RegisterPage />} />
              <Route path="/workers" element={<WorkersPage />} />
              <Route path="/worker/:id" element={<WorkerProfilePage />} />
              <Route path="/booking/:id" element={
                <ProtectedRoute allowedRoles={['customer']}>
                  <BookingPage />
                </ProtectedRoute>
              } />
              <Route path="/dashboard" element={
                <ProtectedRoute allowedRoles={['customer']}>
                  <CustomerDashboard />
                </ProtectedRoute>
              } />
              <Route path="/worker-dashboard" element={
                <ProtectedRoute allowedRoles={['worker']}>
                  <WorkerDashboard />
                </ProtectedRoute>
              } />
              <Route path="/admin" element={
                <ProtectedRoute allowedRoles={['admin']}>
                  <AdminDashboard />
                </ProtectedRoute>
              } />
            </Routes>
          </AnimatePresence>
          
          <footer className="bg-white/80 backdrop-blur-lg border-t border-gray-100 py-12">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
              <div className="grid md:grid-cols-4 gap-8">
                <div>
                  <div className="flex items-center gap-2 mb-4">
                    <div className="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center shadow-lg shadow-primary-500/30">
                      <span className="text-white font-bold text-xl">H</span>
                    </div>
                    <span className="font-display font-bold text-xl text-gray-800">HandsOn</span>
                  </div>
                  <p className="text-gray-600 text-sm">
                    Connecting customers with trusted professionals across Kenya.
                  </p>
                </div>
                <div>
                  <h4 className="font-semibold text-gray-900 mb-4">Services</h4>
                  <ul className="space-y-2 text-sm text-gray-600">
                    <li><a href="#" className="hover:text-primary-600">Cleaning</a></li>
                    <li><a href="#" className="hover:text-primary-600">Electrical</a></li>
                    <li><a href="#" className="hover:text-primary-600">Plumbing</a></li>
                    <li><a href="#" className="hover:text-primary-600">Painting</a></li>
                  </ul>
                </div>
                <div>
                  <h4 className="font-semibold text-gray-900 mb-4">Company</h4>
                  <ul className="space-y-2 text-sm text-gray-600">
                    <li><a href="#" className="hover:text-primary-600">About Us</a></li>
                    <li><a href="#" className="hover:text-primary-600">Careers</a></li>
                    <li><a href="#" className="hover:text-primary-600">Press</a></li>
                    <li><a href="#" className="hover:text-primary-600">Contact</a></li>
                  </ul>
                </div>
                <div>
                  <h4 className="font-semibold text-gray-900 mb-4">Legal</h4>
                  <ul className="space-y-2 text-sm text-gray-600">
                    <li><a href="#" className="hover:text-primary-600">Privacy Policy</a></li>
                    <li><a href="#" className="hover:text-primary-600">Terms of Service</a></li>
                    <li><a href="#" className="hover:text-primary-600">Cookie Policy</a></li>
                  </ul>
                </div>
              </div>
              <div className="mt-12 pt-8 border-t border-gray-100 text-center text-sm text-gray-500">
                © 2026 HandsOn Kenya. All rights reserved.
              </div>
            </div>
          </footer>
        </div>
      </Router>
    </AuthProvider>
  )
}

export default App
