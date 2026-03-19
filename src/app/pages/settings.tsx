import * as React from "react";
import { Link } from "react-router";
import { currentUser } from "../data/mock-data";
import { ArrowUpRight, Camera, Check, X } from "lucide-react";
import { Avatar, AvatarFallback, AvatarImage } from "../components/ui/avatar";

export function SettingsPage() {
  // Düzenleme modlarını takip eden state'ler
  const [editingField, setEditingField] = React.useState<string | null>(null);
  
  // Veri state'leri
  const [email, setEmail] = React.useState("elifalanur7@gmail.com");
  const [username, setUsername] = React.useState(currentUser.username);

  const handleSave = () => {
    setEditingField(null);
    // Buraya ileride backend isteği gelecek
  };

  return (
    <div className="w-full bg-white dark:bg-slate-950 font-sans min-h-full">
      <main className="max-w-3xl mx-auto px-6 py-16">
        
        {/* SADE BAŞLIK */}
        <h1 className="text-4xl md:text-5xl font-bold text-slate-900 dark:text-white tracking-tight mb-16">
          Settings
        </h1>

        <div className="space-y-12 animate-in fade-in slide-in-from-bottom-4 duration-700">
          
          {/* 1. EMAIL BÖLÜMÜ */}
          <div className="flex justify-between items-start border-b border-slate-50 pb-8">
            <div className="flex-1 space-y-1">
              <p className="text-sm font-medium text-slate-900 dark:text-white">Email address</p>
              {editingField === 'email' ? (
                <input 
                  autoFocus
                  type="email" 
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  className="w-full bg-slate-50 border-none rounded-lg px-3 py-2 text-sm text-slate-900 focus:ring-2 ring-teal-500 outline-none"
                />
              ) : (
                <p className="text-sm text-slate-500">{email}</p>
              )}
            </div>
            
            <div className="ml-4 flex gap-3">
              {editingField === 'email' ? (
                <>
                  <button onClick={handleSave} className="text-sm text-teal-600 font-bold hover:text-teal-700">Save</button>
                  <button onClick={() => setEditingField(null)} className="text-sm text-slate-400 hover:text-slate-600">Cancel</button>
                </>
              ) : (
                <button onClick={() => setEditingField('email')} className="text-sm text-slate-800 hover:text-black font-medium">Edit</button>
              )}
            </div>
          </div>

          {/* 2. USERNAME BÖLÜMÜ */}
          <div className="flex justify-between items-start border-b border-slate-50 pb-8">
            <div className="flex-1 space-y-1">
              <p className="text-sm font-medium text-slate-900 dark:text-white">Username and subdomain</p>
              {editingField === 'username' ? (
                <div className="flex items-center bg-slate-50 rounded-lg px-3 py-2 ring-2 ring-teal-500">
                  <span className="text-slate-400 text-sm">@</span>
                  <input 
                    autoFocus
                    type="text" 
                    value={username}
                    onChange={(e) => setUsername(e.target.value)}
                    className="w-full bg-transparent border-none p-0 ml-1 text-sm text-slate-900 focus:ring-0 outline-none"
                  />
                </div>
              ) : (
                <p className="text-sm text-slate-500">@{username}</p>
              )}
            </div>
            
            <div className="ml-4 flex gap-3">
              {editingField === 'username' ? (
                <>
                  <button onClick={handleSave} className="text-sm text-teal-600 font-bold hover:text-teal-700">Save</button>
                  <button onClick={() => setEditingField(null)} className="text-sm text-slate-400 hover:text-slate-600">Cancel</button>
                </>
              ) : (
                <button onClick={() => setEditingField('username')} className="text-sm text-slate-800 hover:text-black font-medium">Edit</button>
              )}
            </div>
          </div>

          {/* 3. PROFİL BİLGİSİ (Linkli) */}
          <div className="flex justify-between items-center border-b border-slate-50 pb-8">
            <div className="space-y-1">
              <p className="text-sm font-medium text-slate-900 dark:text-white">Profile information</p>
              <p className="text-sm text-slate-500">Edit your photo, name, and bio</p>
            </div>
            <Link to="/profile" className="flex items-center gap-3 group">
              <span className="text-sm text-slate-600 group-hover:text-black transition-colors">Elif Çağlayan</span>
              <Avatar className="h-10 w-10 ring-2 ring-slate-100 shadow-sm transition-transform group-hover:scale-105">
                <AvatarImage src={currentUser.avatar} />
                <AvatarFallback className="bg-orange-500 text-white font-bold">E</AvatarFallback>
              </Avatar>
            </Link>
          </div>

          {/* 4. DİĞER LİNKLER */}
          <div className="space-y-8 pt-4">
            <div className="flex justify-between items-center cursor-pointer group">
              <p className="text-sm text-slate-800 dark:text-slate-200 group-hover:text-black transition-colors">Profile design</p>
              <ArrowUpRight className="w-4 h-4 text-slate-300 group-hover:text-slate-600" />
            </div>

            <div className="flex justify-between items-center cursor-pointer group">
              <p className="text-sm text-slate-800 dark:text-slate-200 group-hover:text-black transition-colors">Custom domain</p>
              <p className="text-sm text-slate-400">None</p>
            </div>

            <div className="pt-8 space-y-6 border-t border-slate-100">
              <button className="text-sm text-teal-600 font-medium hover:text-teal-700 block">Deactivate account</button>
              <button className="text-sm text-red-600 font-medium hover:text-red-700 block">Delete account</button>
            </div>
          </div>
        </div>

        <footer className="mt-32 pt-10 border-t border-slate-50 text-[10px] text-slate-300 uppercase tracking-[0.2em] text-center font-bold">
            Postify Professional Edition • 2026
        </footer>
      </main>
    </div>
  );
}